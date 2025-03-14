# pigeon_call_for_mattermost
mattermostから外向けのウェブフックでpigeonによる架電設定をする際に使うapi

## 注意
php 7.3で動かしているので、それ以外のバージョンだと分からない。

/usr/local/bin/配下のリモシェルのため、接続先ホストへのssh-key設定はあらかじめやっておかないとダメ。
また、phpからシェルを実行するため、実行ユーザーは適宜確認しsudoersで許可をしておくように。

ファイル名に"api"が入っているものはapiでの利用が前提。jsonでレスポンスする。

ファイル説明
submit_check_api.php　架電設定の状態を確認するapi
submit_set_api.php　架電設定をするapi
submit_unset_api.php　架電設定を解除するapi
正直上3つのファイルしか使わない。そのほかのファイルはWEBUIで使うためなので必要時は使うよう。
裏でシェルを実行するのは変わらない。
