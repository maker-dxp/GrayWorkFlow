<template>
  <el-form>
    <el-form-item label="Name">
      <el-input v-model.trim="user.name" />
    </el-form-item>
    <el-form-item label="Email">
      <el-input v-model.trim="user.email" />
    </el-form-item>
    <div style="display: flex;flex-flow: row">
      <el-form-item>
        <el-button type="primary" @click="submit">更新信息</el-button>
      </el-form-item>
      <el-form-item style="margin-left: 20px">
        <el-button type="primary">修改密码</el-button>
      </el-form-item>
    </div>
  </el-form>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
        }
      }
    }
  },
  data(){
    return{
      User:{
        name:'',
        email:'',
        password:''
      }
    }
  },
  methods: {
    submit() {
      this.$message({
        message: 'User information has been updated successfully',
        type: 'success',
        duration: 5 * 1000
      })
    }
  }
}
</script>
