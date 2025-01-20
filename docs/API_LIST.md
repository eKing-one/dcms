# API 开发文档

## API 列表

### 1. 用户管理

#### 1.1 登录

- **请求URL**: `?action=login`
- **请求方式**: POST
- **请求参数**:

    ```x-www-form-urlencoded
    nick=<UserName>&password=<Password>
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "登录成功",
        "data": {
            "user_id": "int",
            "token": "string"  // JWT token
        }
    }
    ```

#### 1.2 注册

- **请求URL**: `?action=register`
- **请求方式**: POST
- **请求参数**:

    ```json
    {
        "username": "string",
        "password": "string",
        "email": "string"
    }
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "注册成功",
        "data": {
            "user_id": "int",
            "username": "string",
            "email": "string"
        }
    }
    ```

#### 1.3 退出登录

- **请求URL**: `/api/user/logout`
- **请求方式**: POST
- **请求参数**: 无
- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "退出登录成功"
    }
    ```
