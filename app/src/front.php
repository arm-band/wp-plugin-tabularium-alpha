<?php

namespace Tabularium\app\src;

/**
 * Front: フロント側のフィルターフック処理
 *
 */
class Front
{
    /**
     * var
     */
    protected $c;
    protected $initialize;
    /**
     * コンストラクタ
     */
    function __construct( $c, $i )
    {
        $this->c = $c;
        $this->initialize = $i;
    }

    /**
     * add_post_add_archive
     *
     * desc: 投稿アーカイブをスラッグとして作成する
     */
    public function add_post_add_archive( $args, $post_type ) {
        if ($post_type === 'post') {
            $slug                = $this->initialize->returnSlug();
            $label               = $this->initialize->returnLabel();
            $args['labels']      = [
                'name' => $label
            ];
            $args['has_archive'] = $slug;
            $args['rewrite']     = [
                'slug'       => $slug,
                'with_front' => false,
            ];
        }
        return $args;
    }
}
