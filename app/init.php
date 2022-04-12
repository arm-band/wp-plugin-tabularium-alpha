<?php

namespace Tabularium\app;

/**
 * 初期化・準備
 */
class initialize
{
    /**
     * const
     */
    const TABULARIUM                  = 'tabularium';
    const TABULARIUM_SETTINGS         = 'Tabularium 設定';
    const TABULARIUM_SETTINGS_EN      = self::TABULARIUM . '-settings';
    const TABULARIUM_SLUG             = self::TABULARIUM . '_slug';
    const TABULARIUM_SLUG_VALIDATION  = self::TABULARIUM . '_slug_validation';
    const TABULARIUM_LABEL            = self::TABULARIUM . '_label';
    const TABULARIUM_LABEL_VALIDATION = self::TABULARIUM . '_label_validation';
    const ENCODING                    = 'UTF-8';
    /**
     * var
     */
    protected $c;
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->c = [
            'TABULARIUM'                  => self::TABULARIUM,
            'TABULARIUM_SETTINGS'         => self::TABULARIUM_SETTINGS,
            'TABULARIUM_SETTINGS_EN'      => self::TABULARIUM_SETTINGS_EN,
            'TABULARIUM_SLUG'             => self::TABULARIUM_SLUG,
            'TABULARIUM_SLUG_VALIDATION'  => self::TABULARIUM_SLUG_VALIDATION,
            'TABULARIUM_LABEL'            => self::TABULARIUM_LABEL,
            'TABULARIUM_LABEL_VALIDATION' => self::TABULARIUM_LABEL_VALIDATION,
            'ENCODING'                    => self::ENCODING,
        ];
    }
    /**
     * 定数返し
     *
     * @return array $c クラス内で宣言した定数を出力する
     */
    public function getConstant()
    {
        return $this->c;
    }
    /**
     * htmlspecialchars のラッパー関数
     *
     * esc_html ではクォートもエスケープされてしまうため、JS処理時は不都合がある
     *
     * @param string $str 文字列
     *
     * @return string $ANONYMOUS $str を エスケープして返す(クォートを除く)
     */
    public function _h( $str )
    {
        return htmlspecialchars( $str, ENT_NOQUOTES, self::ENCODING );
    }
    /**
     * returnSlug: スラッグを返す
     *
     * @return array $ANONYMOUS DBに保存されたタクソノミー、または初期値 'post'
     */
    public function returnSlug()
    {
        return get_option( self::TABULARIUM_SLUG ) ? get_option( self::TABULARIUM_SLUG ) : 'post';
    }
    /**
     * returnLabel: ラベルを返す
     *
     * @return array $ANONYMOUS DBに保存されたタクソノミー、または初期値 '投稿'
     */
    public function returnLabel()
    {
        return get_option( self::TABULARIUM_LABEL ) ? get_option( self::TABULARIUM_LABEL ) : '投稿';
    }
    /**
     * インスタンス返し
     *
     * @param  class $i        自分自身。インスタンス化されたInitialize
     *
     * @return array $instance コンストラクタで宣言した文字列の名前のファイルを探し、require_once して new してインスタンスを返す
     */
    public function getInstance( $i )
    {
        $instance = [];
        try {
            $c = self::getConstant();
            if( require_once( __DIR__ . '/src/front.php' ) ) {
                $instance['Front'] = new \Tabularium\app\src\Front( $c, $i );
            }
            else {
                throw new \Exception( 'クラスファイル読み込みに失敗しました: front.php' );
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $instance;
    }
}
