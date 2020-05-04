# manage-api-service

## 部署

### 环境要求
- PHP 7.2 +
- mbstring

### 部署方式

1. 常规部署参照 lumen 框架，安装依赖即可

    ```shell
    composer install
    ```
2. 建议使用 docker 部署，参考如下：、
    
    ```shell
    # 构建基础运行环境镜像(只需执行一次)
    cd docker/php-base
    docker build -t php-base .
    
    # 构建、集成项目
    cd docker/php-{你的运行环境，如 test}
    docker build -t manage-api .
     
    # 运行容器
    docker run -d -p 8000:8000 manage-api
   ```


## 单元测试

在项目构建过程中，将会自动完成单元测试
```shell
vendor/bin/phpunit tests
```


## 调用说明

1. 系统响应按照以下预定格式返回，API 文档中所描述的字段均为 data 结构体内的内容。
    ```json
    {
        "code": 0,
        "message": "",
        "data": [
            {
                "id": "7",
                "email": "test_email1588406595@a.com",
                "password": "19c41824963d46aafc19be7903d0dff6"
            },
            {
                "id": "8",
                "email": "test_email1588406898@a.com",
                "password": "19c41824963d46aafc19be7903d0dff6"
            },
            {
                "id": "10",
                "email": "test_email1588406985@a.com",
                "password": "19c41824963d46aafc19be7903d0dff6"
            }
        ]
    }
    ```

2. 在生产环境(APP_ENV=`production`)时，服务将会开启调用鉴权。需要以 query_strings (即 GET 请求中参数) 的形式传递登录接口(/v1/login)所返回的 token 信息。单次 token 有效时间为 3600 秒。

## 附录

- 错误码配置文件：`app/Exceptions/Dictionary.php`
- 路由配置文件：`routes/web.php`
- Swagger 文档接口：`/docs`
- Swagger UI：`/api/documentation`
