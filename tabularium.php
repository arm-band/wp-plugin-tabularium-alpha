<?php
/*
  Plugin Name: Tabularium
  Plugin URI:
  Description: WordPress の標準の投稿に下層ページとしてアーカイブページを付与するプラグイン(alpha)。 ※ただし、個別記事のURLはスラッグになります。 ※投稿一覧のスラッグは `get_post_type_object( 'post' )->has_archive` で取得できます。
  Version: 0.0.1
  Author: アルム＝バンド
  Author URI:
  License: MIT
*/

namespace Tabularium;

/**
 * Tabularium
 *
 * desc: メイン処理
 */
class Tabularium
{
    /**
     * var
     */
    protected $c;
    protected $instance;
    protected $initialize;
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        try {
            if( !require_once( __DIR__ . '/app/init.php' ) ) {
                throw new \Exception( '初期化ファイル読み込みに失敗しました: init.php' );
            }
        } catch ( \Exception $e ) {
            echo $e->getMessage();
        }

        $this->initialize = new \Tabularium\app\initialize();
        $this->c = $this->initialize->getConstant();
        $this->instance = $this->initialize->getInstance( $this->initialize );
    }
    /**
     * 、プラグインの機能有効化
     */
    public function front_initialize()
    {
        add_filter( 'register_post_type_args', [ $this->instance['Front'], 'add_post_add_archive' ], 10, 2 );
    }
    /**
     * 管理者画面にメニューと設定画面を追加
     */
    public function admin_initialize()
    {
        // メニューを追加
        add_action( 'admin_menu', [ $this, 'tabularium_create_menu' ] );
        // 独自関数をコールバック関数とする
        add_action( 'admin_init', [ $this, 'register_tabularium_settings' ] );
    }
    /**
     * メニュー追加
     */
    public function tabularium_create_menu()
    {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        //  $page_title : 設定ページの `title` 部分
        //  $menu_title : メニュー名
        //  $capability : 権限 ( 'manage_options' や 'administrator' など)
        //  $menu_slug  : メニューのslug
        //  $function   : 設定ページの出力を行う関数
        //  $icon_url   : メニューに表示するアイコン
        //  $position   : メニューの位置 ( 1 や 99 など )
        add_menu_page(
            $this->c['TABULARIUM_SETTINGS'],
            $this->c['TABULARIUM_SETTINGS'],
            'administrator',
            $this->c['TABULARIUM'],
            [ $this, $this->c['TABULARIUM'] . '_settings_page' ],
            'dashicons-book-alt'
        );
    }
    /**
     * コールバック
     */
    public function register_tabularium_settings()
    {
        // register_setting( $option_group, $option_name, $sanitize_callback )
        //  $option_group      : 設定のグループ名
        //  $option_name       : 設定項目名(DBに保存する名前)
        //  $sanitize_callback : 入力値調整をする際に呼ばれる関数
        register_setting(
            $this->c['TABULARIUM_SETTINGS_EN'],
            $this->c['TABULARIUM_SLUG'],
            [ $this, $this->c['TABULARIUM_SLUG_VALIDATION'] ]
        );
        register_setting(
            $this->c['TABULARIUM_SETTINGS_EN'],
            $this->c['TABULARIUM_LABEL'],
            [ $this, $this->c['TABULARIUM_LABEL_VALIDATION'] ]
        );
    }
    /**
     * スラッグのバリデーション。コールバックから呼ばれる
     *
     * @param array $newInput 設定画面で入力されたパラメータ
     *
     * @return string $newInput / $ANONYMOUS バリデーションに成功した場合は $newInput そのものを返す。失敗した場合はDBに保存してあった元のデータを get_option で呼び戻す。
     */
    public function tabularium_slug_validation( $newInput )
    {
        // nonce check
        check_admin_referer( $this->c['TABULARIUM'] . '_options', 'name_of_nonce_field' );

        // validation
        if( preg_match( '/^[\w\d\-_]+$/i', $newInput ) ) {
            return $newInput;
        }
        else {
            // add_settings_error( $setting, $code, $message, $type )
            //  $setting : 設定のslug
            //  $code    : エラーコードのslug (HTMLで'setting-error-{$code}'のような形でidが設定されます)
            //  $message : エラーメッセージの内容
            //  $type    : メッセージのタイプ。'updated' (成功) か 'error' (エラー) のどちらか
            add_settings_error(
                $this->c['TABULARIUM'],
                $this->c['TABULARIUM'] . '_slug-validation_error',
                __(
                    '入力したスラッグが不正です。',
                    $this->c['TABULARIUM']
                ),
                'error'
            );

            return $this->initialize->returnSlug();
        }
    }
    /**
     * ラベルのバリデーション。コールバックから呼ばれる
     *
     * @param array $newInput 設定画面で入力されたパラメータ
     *
     * @return string $newInput / $ANONYMOUS バリデーションに成功した場合は $newInput そのものを返す。失敗した場合はDBに保存してあった元のデータを get_option で呼び戻す。
     */
    public function tabularium_label_validation( $newInput )
    {
        // nonce check
        check_admin_referer( $this->c['TABULARIUM'] . '_options', 'name_of_nonce_field' );

        // validation
        if( mb_strlen( $newInput, $this->c['ENCODING'] ) > 0 ) {
            return $newInput;
        }
        else {
            // add_settings_error( $setting, $code, $message, $type )
            //  $setting : 設定のslug
            //  $code    : エラーコードのslug (HTMLで'setting-error-{$code}'のような形でidが設定されます)
            //  $message : エラーメッセージの内容
            //  $type    : メッセージのタイプ。'updated' (成功) か 'error' (エラー) のどちらか
            add_settings_error(
                $this->c['TABULARIUM'],
                $this->c['TABULARIUM'] . '_label-validation_error',
                __(
                    '入力したラベルが不正です。',
                    $this->c['TABULARIUM']
                ),
                'error'
            );

            return $this->initialize->returnLabel();
        }
    }
    /**
     * 設定画面ページの生成
     */
    public function tabularium_settings_page()
    {
        if( get_settings_errors( $this->c['TABULARIUM'] ) ) {
            // エラーがあった場合はエラーを表示
            settings_errors( $this->c['TABULARIUM'] );
        }
        else if( isset($_GET['settings-updated']) && !empty($_GET['settings-updated']) && true == $_GET['settings-updated'] ) {
            //設定変更時にメッセージ表示
?>
            <div id="settings_updated" class="updated notice is-dismissible"><p><strong>設定を保存しました。</strong></p></div>
<?php
        }
?>

        <div class="wrap">
            <h1><?= esc_html( $this->c['TABULARIUM_SETTINGS'] ); ?></h1>
            <form method="post" action="options.php">
<?php settings_fields( $this->c['TABULARIUM_SETTINGS_EN'] ); ?>
<?php do_settings_sections( $this->c['TABULARIUM_SETTINGS_EN'] ); ?>
                <h2>スラッグ</h2>
                <p>投稿アーアイブのスラッグです。使用できる文字列は小文字の半角英数字とハイフンのみ、空文字は不可です。初期値: <code>post</code></p>
                <table class="form-table" id="<?= esc_attr( $this->c['TABULARIUM_SLUG'] ); ?>-table">
                    <tr>
                        <th></th>
                        <td>
                            <input type="text" name="<?= esc_attr( $this->c['TABULARIUM_SLUG'] ); ?>" id="<?= esc_attr( $this->c['TABULARIUM_SLUG'] ); ?>" value="<?= esc_attr( $this->initialize->returnSlug() ); ?>" required="required">
                        </td>
                    </tr>
                </table>
                <h2>ラベル</h2>
                <p>投稿アーアイブのラベルです。初期値: <code>投稿</code></p>
                <table class="form-table" id="<?= esc_attr( $this->c['TABULARIUM_LABEL'] ); ?>-table">
                    <tr>
                        <th></th>
                        <td>
                            <input type="text" name="<?= esc_attr( $this->c['TABULARIUM_LABEL'] ); ?>" id="<?= esc_attr( $this->c['TABULARIUM_LABEL'] ); ?>" value="<?= esc_attr( $this->initialize->returnLabel() ); ?>" required="required">
                        </td>
                    </tr>
                </table>
<?php wp_nonce_field( $this->c['TABULARIUM'] . '_options', 'name_of_nonce_field' ); ?>
<?php submit_button( '設定を保存', 'primary large', 'submit', true, [ 'tabindex' => '1' ] ); ?>
            </form>
        </div>

<?php
    }
}

// 処理
$wp_ab_tabularium = new Tabularium();

$wp_ab_tabularium->front_initialize();

if( is_admin() ) {
    // 管理者画面を表示している場合のみ実行
    $wp_ab_tabularium->admin_initialize();
}
