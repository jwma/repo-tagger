repo-tagger
===========

一个用 Symfony3 写的个人 GitHub star 仓库的管理工具。

## 部署
安装依赖
````
composer install
````

设置个人的 GitHub 账号密码
````yaml
# app/config/parameters.yml
github_username: your_username
github_password: your_password
````

运行爬虫
````
bin/console app:crawl-starred-list
````

## 运行
运行 Web 服务器
````
bin/console server:run 127.0.0.1:8080
````

待续..