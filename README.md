# Attendance-record

## プロジェクトの概要

**Attendance-record** は、従業員の勤怠情報を簡単に管理・確認できる勤怠管理システムです。
一般ユーザーは自分の出勤・退勤・休憩などの勤怠状況を記録でき、管理者ユーザーは全ユーザーの勤怠状況や修正申請を確認・承認することができます。

## 環境構築手順

1. コンテナを立ち上げるため、以下を実行：

    ```bash
    docker compose up -d --build
    ```

2. `.env` ファイルを作成するため、以下を実行：

    ```bash
    cp src/.env.example src/.env
    ```

3. PHP コンテナに入るため、以下を実行：

    ```bash
    docker compose exec php bash
    ```

4. Composer パッケージをインストールするため、以下を実行：

    ```bash
    composer install
    ```

5. アプリケーションキーを作成するため、以下を実行：

    ```bash
    php artisan key:generate
    ```

6. マイグレーションを実行するため、以下を実行：

    ```bash
    php artisan migrate
    ```

    php artisan db:seed

## 使用技術

- **フレームワーク**: Laravel
- **言語**: PHP, HTML, CSS, JavaScript
- **データベース**: MySQL
- **Webサーバー**: nginx
- **開発環境**: Docker, Docker Compose
- **テスト**: PHPUnit

## 管理者ユーザーおよび一般ユーザーのログイン情報

### 管理者ユーザー

- **メールアドレス**: `admin@example.com`
- **パスワード**: `admin1234`

### 一般ユーザー

- **メールアドレス**: `user1@example.com`
- **パスワード**: `user1234`
