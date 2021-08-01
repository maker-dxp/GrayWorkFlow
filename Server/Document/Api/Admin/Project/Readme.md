# 灰风工作平台管理员项目API


## 获取任务项目列表

Method : GET

URL :  /api/Admin/Project/list

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

等后期再加其他选项

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