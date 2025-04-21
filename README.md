# Attendance-record


## 環境構築手順

-   コンテナを立ち上げるため、以下を実行

```
docker compose up -d --build
```

-   env ファイルの作成をするため、以下を実行

```
cp src/.env.example src/.env
```

-   php にコンテナに入るため、以下を実行

```
docker compose exec php bash
```

-   composer パッケージをインストールするため、以下を実行

```
composer install
```

-   アプリケーションキーを作成するため、以下を実行

```
php artisan key:generate
```

-   マイグレーションを実行するため、以下を実行

```
php artisan migrate
```

## 開発でやる必要があること(この手順はアプリ完成時には README から削除する)

-   view ファイルの作成・修正・削除
-   controller の作成・修正
-   model の作成・修正
-   css の作成・修正・削除(クラス名も直すこと)
-   migration ファイルの作成・修正
-   seeder の作成
-   README.md(このファイル)の修正
