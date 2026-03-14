# API 开发文档

API地址：https://[网站域名]/api.php

支持的登录认证方式：Session、Cookie、Authorization

## API 列表

### 0. 系统功能

#### 0.0 Hello world

- **请求URL**: `?`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "Hello <UserName>"
    }
    ```

#### 0.1 获取验证码

- **请求URL**: `?action=get-captcha-url`
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
    nick=<string>&password=<string>&aut_save=<int>
    ```

    参数说明：nick -> 用户名、password -> 密码、aut_save -> 是否记住密码（可选，值为“1”就记住密码）

- **响应内容**:

    ```json
    {
        "status": "success",
        "message": "login successful",
        "data": {
            "user_id": "int",
            "login_id": "int",
            "token": "string",  // JWT token
            "expiration": "int"
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

- **请求URL**: `?action=logout`
- **请求方式**: POST
- **请求参数**: 无
- **响应内容**:

    ```json
    {
        "status": "success"
    }
    ```

#### 1.4 用邮箱找回密码

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

#### 1.5 重置密码（这个功能还没有做）

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
            "user_id": "<int>"
        }
    }
    ```

#### 1.6 获取当前在线用户列表

- **请求URL**: `?action=online-users`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "users": [
            {
                "id": "<int>",
                "last_online": "<Y-m-d H:i:s>"
            },
            {...}
        ]
    }
    ```

#### 1.7 获取用户信息

- **请求URL**: `?action=user-info&id=<int>`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "data": [
            {
                "id": "<int>",
                "nick": "<string>",
                "date_reg": "<int>",
                "balls": "<int>",
                "browser": "<string>",
                "money": "<int>",
                "group_name": "<string>",
                "pol": "<int>",
                "date_last": "<int>"
            }
        ]
    }
    ```

    id: 用户ID，nick: 用户昵称，date_reg: 注册时间，balls: 积分？，browser: 浏览器类型，money: 硬币，pol: 性别，date_last: 最后在线时间

### 2. 留言板相关

#### 2.1 获取留言板列表

- **请求URL**: `?action=guest-msg-list&page=<int>`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "data": [
            {
                "id": "<int>",
                "id_user": "<int>",
                "time": "<int>",
                "msg": "<string>"
            },
            {...}
        ],
        "all_pages": "<int>"
    }
    ```

#### 2.2 添加留言

- **请求URL**: `?action=guest-msg-add`
- **请求方式**: POST

- **请求参数**(已登录):

    ```x-www-form-urlencoded
    msg=<string>
    ```

- **请求参数**(未登录):

    ```x-www-form-urlencoded
    msg=<string>&captcha=<string>&captcha_token=<string>
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "id": "<int>"
    }
    ```

- **可能的报错内容**:

    缺少信息内容:

    ```json
    {
        "status": "error",
        "message": "msg not found"
    }
    ```

    在信息文本中发现了一个禁止字符:

    ```json
    {
        "status": "error",
        "message": "forbidden strings: <string>"
    }
    ```

    内容过长:

    ```json
    {
        "status": "error",
        "message": "content too long"
    }
    ```

    内容过短:

    ```json
    {
        "status": "error",
        "message": "content too short"
    }
    ```

    需要登录:

    ```json
    {
        "status": "error",
        "message": "not login"
    }
    ```

#### 2.3 查看当前在留言板的用户

- **请求URL**: `?action=guest-users-list&page=<int>`
- **请求方式**: GET
- **响应内容**:

    ```json
    {
        "status": "success",
        "data": [
            {
                "id_user": "<int>",
                "last_online": "<Y-m-d H:i:s>"
            },
            {...}
        ],
        "all_pages": "<int>"
    }
    ```

#### 2.4 删除留言板信息

- **请求URL**: `?action=guest-msg-delete`
- **请求方式**: POST

- **请求参数**:

    ```x-www-form-urlencoded
    id=<int>
    ```

- **响应内容**:

    ```json
    {
        "status": "success"
    }
    ```

- **可能的报错内容**:

    需要登录:

    ```json
    {
        "status": "error",
        "message": "not login"
    }
    ```

    无权限:

    ```json
    {
        "status": "error",
        "message": "no permissions"
    }
    ```

    缺少 id 参数:

    ```json
    {
        "status": "error",
        "message": "msg id not found"
    }
    ```

    消息不存在:

    ```json
    {
        "status": "error",
        "message": "msg not exist"
    }
    ```

### 3. 聊天室相关

#### 3.1 获取聊天室列表

- **请求URL**: `?action=chat-rooms-list`
- **请求方式**: GET
- **响应内容**:

    ```json
    {
        "status": "success",
        "data": [
            {
                "id": "<int>",
                "pos": "<int>",
                "name": "<string>",
                "umnik": "<string>",
                "shutnik": "<string>",
                "opis": "<string>"
            },
            {...}
        ]
    }
    ```

    id: 聊天室ID，pos: 排序ID，name: 聊天室名称，umnik：是否启用答题机器人，shutnik: 是否启用笑话机器人，opis: 聊天室描述

#### 3.2 获取聊天内容列表

- **请求URL**: `?action=chat-msg-list&room=<int>&page=<int>`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "data": [
            {
            "id": "<int>",
            "room": "<int>",
            "id_user": "<int>",
            "time": "<int>",
            "msg": "<string>",
            "vopros": "<int>",
            "umnik_st": "<string>",
            "shutnik": "<string>",
            "privat": "<int>"
            },
            {...}
        ],
        "all_pages": "<int>"
    }
    ```

    id: 信息ID，id_user: 用户ID，umnik_st：答题机器人的信息，shutnik: 笑话机器人的信息，privat: 私聊对象

- **可能的报错内容**:

    缺少`room`参数:

    ```json
    {
        "status": "error",
        "message": "room id not found"
    }
    ```

    请求的聊天室ID无效:

    ```json
    {
        "status": "error",
        "message": "room not found"
    }
    ```

#### 3.3 获取聊天内容列表

- **请求URL**: `?action=chat-msg-get&room=<int>&id=<int>`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "data": [
            {
            "id": "<int>",
            "room": "<int>",
            "id_user": "<int>",
            "time": "<int>",
            "msg": "<string>",
            "vopros": "<int>",
            "umnik_st": "<string>",
            "shutnik": "<string>",
            "privat": "<int>"
            },
            {...}
        ]
    }
    ```

- **可能的报错内容**:

    缺少`room`参数:

    ```json
    {
        "status": "error",
        "message": "room id not found"
    }
    ```

    请求的聊天室ID无效:

    ```json
    {
        "status": "error",
        "message": "room not found"
    }
    ```

    缺少`id`参数:

    ```json
    {
        "status": "error",
        "message": "msg id not found"
    }
    ```

#### 3.4 添加聊天信息

- **请求URL**: `?action=chat-msg-add&room=<int>`
- **请求方式**: POST

- **请求参数**:

    ```x-www-form-urlencoded
    msg=<string>
    ```

- **响应内容**:

    ```json
    {
        "status": "success",
        "id": "<int>"
    }
    ```

- **可能的报错内容**:

    缺少信息内容:

    ```json
    {
        "status": "error",
        "message": "msg not found"
    }
    ```

    在信息文本中发现了一个禁止字符:

    ```json
    {
        "status": "error",
        "message": "forbidden strings: <string>"
    }
    ```

    内容过长:

    ```json
    {
        "status": "error",
        "message": "content too long"
    }
    ```

    内容过短:

    ```json
    {
        "status": "error",
        "message": "content too short"
    }
    ```

    需要登录:

    ```json
    {
        "status": "error",
        "message": "not login"
    }
    ```

### 4. 日记相关

#### 4.1 查看日记列表

- **请求URL**: `?action=note-list&page=<int>`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "data": {
            "<int>": {
                "title": "<string>|NULL",
                "date": "<string>",
                "id_user": "<int>",
                "count": "<int>",
                "id_dir": "<int>",
                "type": "<int>",
                "private": "<int>"
            },
            "<int>": {...}
        },
        "all_pages": "<int>"
    }
    ```

    id: 日记ID

#### 4.2 查看某篇日记

- **请求URL**: `?action=note-get&id=<int>&page=<int>`
- **请求方式**: GET

- **响应内容**:

    ```json
    {
        "status": "success",
        "data": {
            "title": "<string>|NULL",
            "msg": "<string>|NULL",
            "date": "<string>",
            "id_user": "<int>",
            "count": "<int>",
            "id_dir": "<int>",
            "type": "<int>",
            "private": "<int>"
            "comment_list": [
                {
                    "id": "<int>",
                    "id_user": "<int>",
                    "id_notes": "<int>",
                    "msg": "<string>|NULL",
                    "time": "<int>"
                },
                {...}
            ]
        },
        "all_comment_pages": "<int>"
    }
    ```

    id: 日记ID

#### 4.3 添加日记

- **请求URL**: `?action=note-add`
- **请求方式**: POST

- **请求参数**:

    ```x-www-form-urlencoded
    title=<string>&msg=<string>&chislo=<string>&captcha_token=<string>&private=<int>&private_komm=<int>
    ```

    title: 日记标题，msg: 日记正文，chislo: 验证码，captcha_token：验证码token，private: 隐私设置(0->公开, 1->仅好友可见, 2->仅自己可见)，private_komm: 隐私密码
    * 管理员不需要虽然验证码和验证码token；隐私设置和密码是可选项

- **响应内容**:

    ```json
    {
        "status": "success",
        "id": "<int>"
    }
    ```

- **可能的报错内容**:

    缺少标题：

    ```json
    {
        "status": "error",
        "message": "title not found"
    }
    ```

    标题过长:

    ```json
    {
        "status": "error",
        "message": "title too long"
    }
    ```

    缺少信息内容:

    ```json
    {
        "status": "error",
        "message": "msg not found"
    }
    ```

    内容过长:

    ```json
    {
        "status": "error",
        "message": "content too long"
    }
    ```

    内容过短:

    ```json
    {
        "status": "error",
        "message": "content too short"
    }
    ```

    需要登录:

    ```json
    {
        "status": "error",
        "message": "not login"
    }
    ```
