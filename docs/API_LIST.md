# API 开发文档

## API 列表

### 0. 系统功能

#### 0.1 获取验证码

- **请求URL**: `?action=get_captcha_url`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "captcha_token": "string",
        "captcha_url": "string"
    }
    ```

### 1. 用户管理

#### 1.1 登录

- **请求URL**: `?action=login`
- **请求方式**: POST
- **请求参数**:

    ```x-www-form-urlencoded
    nick=<string>&password=<string>
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "login successful",
        "data": {
            "user_id": "int",
            "token": "string"  // JWT token
        }
    }
    ```

- **可能的报错内容**:

    用户名或密码错误:

    ```json
    {
        "status": "error",
        "message": "Incorrect username or password"
    }
    ```

    缺少`nick`或`password`参数:

    ```json
    {
        "status": "error",
        "message": "Missing required parameters"
    }

#### 1.2 注册

- **请求URL**: `?action=register`
- **请求方式**: POST
- **请求参数**:

    ```x-www-form-urlencoded
    reg_nick=<string>&password=<string>&captcha=<string>&captcha_token=<string>&email=<string>&pol=<1 or 0>

    # pol参数为可选项
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "registration successful",
        "data": {
            "user_id": "int",
        }
    }
    ```

    如果开启了影响验证：

    ```json
    {
        "status": "success",
        "message": "verification email sent",
        "data": {
            "user_id": "int",
        }
    }
    ```

- **可能的报错内容**:

    已关闭注册:

    ```json
    {
        "status": "error",
        "message": "registration is closed"
    }
    ```

    缺少验证码:

    ```json
    {
        "status": "error",
        "message": "verification code is required"
    }
    ```

    captcha_token 格式错误:

    ```json
    {
        "status": "error",
        "message": "captcha_token format error"
    }
    ```

    captcha_token 已过期:

    ```json
    {
        "status": "error",
        "message": "captcha_token expired"
    }
    ```

    captcha_token 无效或已使用:

    ```json
    {
        "status": "error",
        "message": "captcha_token invalid or used"
    }
    ```

    验证码错误:

    ```json
    {
        "status": "error",
        "message": "incorrect verification code"
    }
    ```

    缺少昵称:

    ```json
    {
        "status": "error",
        "message": "nick is missing"
    }
    ```

    缺少密码:

    ```json
    {
        "status": "error",
        "message": "password is missing"
    }
    ```

    缺少电子邮箱参数:

    ```json
    {
        "status": "error",
        "message": "email is missing"
    }
    ```

    邮箱地址格式错误:

    ```json
    {
        "status": "error",
        "message": "invalid email address"
    }
    ```

    昵称含有非法字符:

    ```json
    {
        "status": "error",
        "message": "invalid characters in nick"
    }
    ```

    昵称短于3个字符:

    ```json
    {
        "status": "error",
        "message": "nick too short"
    }
    ```

    昵称长度超过32个字符:

    ```json
    {
        "status": "error",
        "message": "nick too long"
    }
    ```

    用户名已注册:

    ```json
    {
        "status": "error",
        "message": "nick already registered"
    }
    ```

    电子邮件已注册:

    ```json
    {
        "status": "error",
        "message": "email already registered"
    }
    ```

    密码长度不能短于6个字符:

    ```json
    {
        "status": "error",
        "message": "password too short"
    }
    ```

    密码长度超过32个字符:

    ```json
    {
        "status": "error",
        "message": "password too long"
    }
    ```

    注册验证邮件已发送:

    ```json
    {
        "status": "success",
        "message": "verification email sent"
    }
    ```

    验证邮件发送失败:

    ```json
    {
        "status": "error",
        "message": "email sending failed: <错误原因>"
    }
    ```

#### 1.3 退出登录

- **请求URL**: `/api/user/logout`
- **请求方式**: POST
- **请求参数**: 无
- **响应内容**:

    ```json
    {
        "status": "success"
    }
    ```

### 1.4 用邮箱找回密码

- **请求URL**: `?action=forgot-password`
- **请求方式**: POST
- **请求参数**:

    ```x-www-form-urlencoded
    nick=<string>&email=<string>&captcha=<string>&captcha_token=<string>
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "password reset email sent",
        "data": {
            "email": "<string>"
        }
    }
    ```

- **可能的报错内容**:

    邮件发送失败:

    ```json
    {
        "status": "error",
        "message": "email sending failed: <错误原因>"
    }
    ```

    邮箱地址错误:

    ```json
    {
        "status": "error",
        "message": "invalid email address"
    }
    ```

    昵称不存在:

    ```json
    {
        "status": "error",
        "message": "nick not found"
    }
    ```

    缺少参数:

    ```json
    {
        "status": "error",
        "message": "missing parameters"
    }
    ```

### 1.5 重置密码（这个功能还没有做）

- **请求URL**: `?action=reset-password`
- **请求方式**: POST
- **请求参数**:

    ```x-www-form-urlencoded
    nick=<string>&email=<string>&token=<string>&password=<string>
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "reset password successfully",
        "data": {
            "user_id": "int"
        }
    }
    ```
