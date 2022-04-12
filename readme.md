# Tabularium

## Abstract

WordPress の標準の投稿に下層ページとしてアーカイブページを付与するプラグイン(alpha)。

- ただし、個別記事のURLはスラッグになります。
- 投稿一覧のスラッグは `get_post_type_object( 'post' )->has_archive` で取得できます。