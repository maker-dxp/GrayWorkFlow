# 灰风工作平台公开项目API


## 获取任务项目列表

###注意: 此API为普通用户API 请后端务必根据当前用户职位权限过滤相应的可接受的项目

Method : GET

URL :  /api/Project/list

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> | Access-Token | [Jwt-Token] |

Params:

    {
        page:0  // 页面数
        limit:10 // 一页显示的数量   
    }

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
            Projects:[
                {
                    id: 0,
                    ProjectType: (String),
                    ProjectName: (String),
                    Project_CreateAt: (YYYY-MM-DD),
                    ProjectStaff: [
                        (String)   // 这个数组里面 返回当前任务已经被接受的职位 现有职位参考 职位API的 注意事项  
                    ]
                }
            ] 
        }
    }

- - -

###

Method : POST

URL :  /api/Project/accept

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> | Access-Token | [Jwt-Token] |

Params:

    {
        ProjectId:(Number)  //传递想接受任务的 id 此id为数据库主键 
    }

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
        message:'接受项目成功！',
        data:{
            ProjectId:(Number)
            AcceptTime:(YYYY-MM-DD)
        }
    }

- - -


