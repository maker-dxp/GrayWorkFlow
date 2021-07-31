# 灰风字幕组API文档

## 注意事项:

请不要把管理员级别操作 和普通API混合在一起！

任何管理员操作都应该单独做API！

各位后端 麻烦更新API文档时候

务必遵循 API 文档格式！！


<details>
  <summary>参考:</summary>
登录

Method : POST

URL :  /api/User/login

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> |  none | none |

Params:

    {
        "UserName": (String),
        "Password": (String)
    }
----------------------------------------
Response:

>Header:
>
>|  Key   | Value  |
>|  ----  | ----  |
>| Access-Token: | [jwt-token] |

Body:

    {
        code:200,
        message:'成功',
        data:{}
    }

</details>


- - -

API导航:

[用户API](#UserApi)

[项目API](#ProjectApi)

[职位API](#ProfessionApi)


- - -
用户API的注意事项:

<span id="UserApi"></span>

[用户API](./User/Readme.md)


除去登录API 之外的所有API 其余全部API 请务必携带

Access-Token 头 ，否则会被400

一些通用的用户错误码



## Code:
 | code | message |
 | ---- | ---- |
 | 200  |   OK    |
 | 2000 | 登陆成功 |
 | 2001 | 注册成功 |
 | 400  | 非法请求 |
 | 4000 | Body为空 |
 | 4001 | JSON格式有误|
 | 4002 | Body字段缺失|
 | 401 | 未登录 |
 | 4010 | 用户名或密码不正确 |
 | 4011 | 非法Token |
 | 4012 | Token已过期 |
 | 403 | 禁止访问 |
 | 405 | 方法不允许 |
 | 409 | 存在冲突 |
 | 4090 | 用户已存在 |

 
- - -

<span id="ProjectApi"></span>

没什么要注意的

[项目API](./Project/Readme.md)

- - - 

<span id="ProfessionApi"></span>

没什么要注意的

[职位API](./Profession/Readme.md)


- - -



