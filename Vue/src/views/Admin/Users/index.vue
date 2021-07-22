<template>
  <div class="default-layout">
    <div class="user-table">
      <div class="user-action">
        <el-button @click="dialogVisible = true" type="primary">+ 增加用户</el-button>
      </div>
      <el-table
        :data="Users"
        stripe
        :default-sort = "{prop: 'id', order: 'descending'}"
      >
        <el-table-column
          label="用户ID"
          prop="id"
          sortable
        ></el-table-column>
        <el-table-column
          label="用户名"
          prop="Name"
        >
        </el-table-column>
        <el-table-column
          label="职位"
        >
          <template slot-scope="scope">
            <span>{{ UserType(scope.row.Type) }}</span>
          </template>
        </el-table-column>
        <el-table-column
          label="用户详情"
        >
          <template slot-scope="scope">
            <el-button @click="$router.push('UserDetails')" type="text" size="small">查看</el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog
        title="提示"
        :visible.sync="dialogVisible"
        width="50%"
        >
        <el-form
          ref="form" :model="User"
          label-width="80px"
        >
          <el-form-item label="用户名:">
            <el-input v-model="User.Name"></el-input>
          </el-form-item>
          <el-form-item label="用户类型:">
            <el-select v-model="User.Type" placeholder="请选择">
              <el-option
                v-for="item in UserTypeList"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label-width="120px" label="服务器生成密码:">
            <el-radio-group v-model="ServerCreatePassword">
              <el-radio :label="true">是</el-radio>
              <el-radio :label="false">否</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item
            label="用户密码:"
            v-if="ServerCreatePassword === false"
          >
            <el-input></el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
    <el-button @click="dialogVisible = false">取 消</el-button>
    <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
  </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
export default {
  name: "Admin_Users",
  data(){
      return{
        dialogVisible:false,
        ServerCreatePassword:true,
        UserTypeList:[
          {
            value:0,
            label:'管理员'
          }
        ],
        User:{
          Name:'',
          Type:null,
          PassWord:''
        },
        Users:[
          {
            id:0,
            Name:'山酱',
            Type:0,
          }
        ],
        TypeName:{
          0:'管理员',
          1:'翻译'
        }
      }
  },
  methods:{
    UserType(TypeId){
      return this.TypeName[TypeId]
    }
  }
}
</script>

<style scoped>
.user-action{
  padding: 10px 0;
}
.user-table{
  padding: 0 20px;
  background-color: white;
}
</style>
