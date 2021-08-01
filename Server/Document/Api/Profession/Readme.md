# 灰风工作平台职位API

现有职位: 

    Professions: [
        'admin',
        'super_admin',
        'translation',
        'translation_proofreading',
        'time_axis',
        'time_axis_proofreading',
        'subtitle',
        'subtitle_proofreading',
        'compression',
        'back_support',
      ]

职位目前固定 等进一步的更新API

## 获取职位

Method : GET

URL :  /api/Professions/list

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> | Access-Token | [Jwt-Token] |

Params:none

----------------------------------------

Response:

>Header:
>
>|  Key   | Value  |
>|  ----  | ----  |
>| none | none |

Body:

    {
        code:200,
        message:'成功',
        data:{
            Profession:[(String)] //返回现有职位数组  职位为字符串 
        }
    }

- - -