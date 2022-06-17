# API文档

## 更新日志

| 日期      | 版本                   | 更新内容                                                     |
| --------- | ---------------------- | ------------------------------------------------------------ |
| 2021/1/19 | 2.0.0_20210119.1_alpha | 上线腾讯云api网关，使用新api域名接口                         |
| 2021/1/19 | 2.0.0_20210119.2_alpha | 上线获取token接口和更新token的服务端                         |
| 2021/1/20 | 2.0.2_20210119.1_alpha | 基本信息接口未上线的全部上线，已上线的全部升级接入2.0版本的鉴权系统 |



## 响应体格式

```json
{"status":200,"date":"test"}
```

状态码信息：

| 状态码 | 状态信息                     |
| ------ | ---------------------------- |
| 200    | 请求正常                     |
| 201    | 执行正常                     |
| 300    | token错误或token过期         |
| 301    | 用户名或密码错误             |
| 400    | 查询错误或查询结果为空       |
| 401    | 执行错误，包括越权或非法执行 |

## 鉴权相关

### Simple Authentication_v2.0

Simple Authentication是一套类似于Oauth的自研鉴权机制

![鉴权机制v2.drawio](E:\工程\SurvivalCityUserSystem\鉴权机制v2.drawio.svg)

### 0.1 获取acckey(online)

该接口是鉴权机制升级后加入的接口。

该接口向授权服务器发送请求，服务器返回acckey

权限：对所有人公开

方式：`GET`

地址：http://api.survivalcity.cn/release/acckey

参数：

| key      | value  |
| -------- | ------ |
| username | 用户名 |
| password | 密码   |

返回JSON参数：

| 参数     | 含义                   |
| -------- | ---------------------- |
| status   | 状态码，详见状态码章节 |
| acckey   | 获取的acckey           |
| username | 获取acckey的用户名     |

示例：

```
http://api.survivalcity.cn/release/acckey?username=test&password=12345678
```

返回示例：

```json
{
    "status":201,
    "username":"test",
    "acckey":"197623e784d51b5fc863e12e749774b6",
}
```

### 0.2 验证acckey(online)

该接口返回acckey所对应的用户输入的用户名和密码，且请求一次以后该acckey将销毁

权限：仅对服务端开放

方式：`GET`

地址：http://api.survivalcity.cn/release/accserver

参数：

| key    | value              |
| ------ | ------------------ |
| acckey | 客户端提交的acckey |

返回JSON参数：

| 参数   | 含义                             |
| ------ | -------------------------------- |
| acckey | 数据库中储存的acckey             |
| time   | 数据库中储存的生成acckey的时间戳 |

错误代码：

| 代码  | 含义                     |
| ----- | ------------------------ |
| false | acckey已过期或acckey为空 |

请求示例：

```
http://api.survivalcity.cn/release/accserver?acckey=test
```

返回示例：

1. 请求成功

   ```json
   {
       "status":200,
       "username":"test",
       "acckey":"ccde3c5284861ebac208ea8293e9cfa3",
       "time":1642508133
   }
   ```

2. 请求失败

   ```json
   false
   ```

### 0.3 获取token(online)

该接口通过鉴别acckey后，生成token，并直接返回该token

权限：对所有人公开

方式：`GET`

地址：[http://api.survivalcity.cn/release/token]()

参数：

| key    | value                          |
| ------ | ------------------------------ |
| acckey | 从acckey服务器获取的acckey参数 |

示例：

```
https://ai.lmceric.top/php/token.php?acckey=tests
```

返回示例：

```json
{
    "status":200,
    "token":"12390e65b2efbb08612dbeb26a321e3d"
}
```

**注意：token的错误代码格式不同于其它接口，如果请求正确将直接返回token，其错误代码格式如下**

| 错误代码 | 错误信息               |
| -------- | ---------------------- |
| false    | 请求错误或用户名不存在 |
| pfalse   | 密码错误               |

### 0.4 更新token(online)

改接口为操作接口，使用token发出请求将更新token时间，保持token不过期.用户进行每一次操作都必须刷新token，否则token将过期，用户必须重新鉴权获取token

权限：仅限该token

方式：`POST`

地址：http://api.survivalcity.cn/release/refresh

参数：

| key   | value             |
| ----- | ----------------- |
| token | 该用户的token的值 |

返回JSON参数：

| 参数      | 含义                   |
| --------- | ---------------------- |
| status    | 状态码                 |
| old_token | 以过期的token          |
| token     | 刷新后的token          |
| time_out  | 有效期(秒为单位)10分钟 |

请求示例：

```
http://api.survivalcity.cn/release/refresh?token=12390e65b2efbb08612dbeb26a321e3d
```

返回示例：

```json
{
    "status":201,
    "token":"197623e784d51b5fc863e12e749774b6",
    "time_out":600
}
```



## 基本信息相关

### 1.1 注册用户(online)

该接口为操作接口，通过用户名和密码注册用户。

权限：对所有人公开

方式：`GET`

地址：http://api.survivalcity.cn/release/user/signup

参数：

| key      | value        |
| -------- | ------------ |
| username | 输入的用户名 |
| password | 输入的密码   |

返回JSON参数

| 参数     | 含义                           |
| -------- | ------------------------------ |
| status   | 状态码                         |
| username | 从数据库返回的用户注册的用户名 |

示例：

```
http://api.survivalcity.cn/release/user/signup?username=test&password=12345678
```

返回示例：

```json
{
    "status":201,
    "data":{
        "username":"test",
        "user_id":1
    }
}

```

### 1.2 获取用户ID(online)

该接口通过用户的用户名获取用户id信息，直接返回用户id信息

权限：对所有人公开 

方式：`GET`

地址：http://api.survivalcity.cn/release/user/id

参数：

| key      | value        |
| -------- | ------------ |
| username | 用户的用户名 |

返回JSON参数：

| 参数    | 含义                   |
| ------- | ---------------------- |
| status  | 状态码，详见状态码章节 |
| user_id | 该token所对应的id      |

示例：

```
http://api.survivalcity.cn/release/user/id?username=test
```

返回示例：

```json
{
    "status":200,
    "data":{
		"username":"test",
		"user_id":1
	}
}
```

### 1.3 获取基本信息(online)

获取基本信息首先需要获取用户ID，本接口返回用户ID、用户名、用户头像和绑定游戏账户名称

权限：仅对该用户开放

方式：`GET`

地址：http://api.survivalcity.cn/release/user/getinfo

参数：

| key     | value               |
| ------- | ------------------- |
| user_id | 用户id              |
| token   | 鉴权系统获取的token |

返回JSON参数：

| 参数        | 含义                                                   |
| ----------- | ------------------------------------------------------ |
| status      | 状态码，详见状态码章节                                 |
| username    | 该用户ID所对应的用户名                                 |
| user_id     | 该用户id                                               |
| avatar      | 该用户头像，为base64格式                               |
| gameaccount | 该用户所绑定的游戏账户，若为NULL则该用户未绑定游戏账户 |

示例：

```
http://api.survivalcity.cn/release/user/getinfo?token=12390e65b2efbb08612dbeb26a321e3d&user_id=1
```

返回示例：

```json
{
    "status": 200,
    "data": 
        {
            "username": "admin",
            "data": {
                "username": "admin",
                "user_id": 1,
                "avatar": "",//base64格式数据
                "gameaccount":"SimpleAstronaut",//若返回NULL，则该用户未绑定游戏账号
            }
        }
    
}
```

### 1.4 修改基本信息(online)

该接口为操作接口，能对用户基本信息进行添加、修改、删除操作

**删除操作暂未上线**

权限：仅限被操作账户本人或管理员调用

方式：`POST`

地址：http://api.survivalcity.cn/release/user/changeinfo

参数：

| key   | value                             |
| ----- | --------------------------------- |
| token | 鉴权机制获取的该用户下的有效token |
| mode  | 需要进行的操作                    |
| type  | 需要操作的个人信息类型            |
| value | 添加或修改的内容主体              |

注：

1. mode参数包含三个功能，如下所示
   * `add`：添加用户信息信息。注意：此接口进行操作的对象仅限已注册用户，用户注册操作详见“用户注册”章节
   * `change`：修改用户信息，若该用户被修改参数尚未被添加则自动添加信息
   * `delete`：删除用户信息
2. type参数包含如下内容
   * `username`：用户名
   * `avatar`：用户头像
   * `gameaccount`：用户绑定的游戏账户
   * `password`：密码

返回JSON参数：

| 参数       | 含义                         |
| ---------- | ---------------------------- |
| status     | 状态码，详见状态码章节       |
| latestinfo | 返回修改完成后的的参数的信息 |

请求示例：

```
https://ai.lmceric.top/php/user.php?token=12390e65b2efbb08612dbeb26a321e3d&mode=add&type=gameaccount&value=SimpleAstronaut
```

返回示例：

```json
{
    "status":201,
    "latestinfo":{
        "gameaccount":"SimpleAstronaut"
    }
}
```

### 1.5 获取状态信息(online)

该接口返回用户的账户状态信息和通行证状态信息

方式：`GET`

地址：http://api.survivalcity.cn/release/user/status

参数：

| key     | value                   |
| ------- | ----------------------- |
| token   | 鉴权机制获取的有效token |
| user_id | 查询的用户的用户ID      |

返回JSON参数：

| 参数        | 含义           |
| ----------- | -------------- |
| status      | 状态码         |
| user_id     | 查询对象用户id |
| user_state* | 账户状态       |
| user_class* | 账户等级       |
| licence*    | 许可证等级     |

* 账户状态返回INT类型数据，返回0-1-2三种状态，0代表封禁状态，1代表正常状态，2代表特殊状态
* 账户等级返回INT类型数据，返回0-1-2三种数据，0代表管理用户，通常是管理员的账户，1代表普通用户，2代表特殊贡献或特殊标记账户
* 许可证等级返回INT类型数据，返回0-1-2-3-4四种数据，0代表无许可证状态，1代表月度许可证，2代表季度许可证，3代表年度许可证，4代表永久许可证（通常仅限于管理员账户和特殊贡献账户）

请求示例：

```
http://api.survivalcity.cn/release/user/status?token=12390e65b2efbb08612dbeb26a321e3d&user_id=12
```

返回示例：

```json
{
    "status":200,
    "data":{
        "user_id":12,
        "user_state":1,
        "user_class":0,
        "licence":4
    }
}
```



## 游戏账户相关

### 获取游戏账户

### 添加游戏账户

```json
{
    "status":201,
    "data":{
        "username":"test",
        "user_id":1
    }
}
```

