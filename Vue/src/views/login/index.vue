<template>
  <div>
    <el-row class="login-container" type="flex">
      <el-col :xs="0" :sm="12" :md="16" :lg="18">
        <el-image
          style="width: 100%; height: 100%"
          :fit="'cover'"
          :src="require('@/assets/login_background.png')"
        ></el-image>
      </el-col>
      <el-col style="padding: 0 20px" :xs="24" :sm="12" :md="8" :lg="6">
        <div style="height: 33%;padding-top: 20px" class="login-form-icon-container login-form-container">
          <el-image
            style="width: 150px;height: 150px;"
            :src="require('@/assets/icon.jpg')"
            :fit="'cover'"
          >
          </el-image>
          <div style="margin-top: 7px;font-size: x-large">灰风字幕组</div>
          <div style="margin-top: 12px">工作平台</div>
        </div>
        <div style="height: 66%" class="login-form login-form-container">
          <el-form
            ref="loginForm"
            :model="loginForm"
            :rules="loginRules"
          >
            <el-form-item prop="UserName">
              <el-input type="text" v-model="loginForm.UserName" placeholder="账户名"/>
            </el-form-item>
            <el-form-item prop="PassWord">
              <el-input v-model="loginForm.PassWord" type="password" placeholder="密码"/>
            </el-form-item>
            <el-form-item>
              <div style="display: flex;justify-content: space-between">
                <el-checkbox
                  label="30天内记住密码"
                  name="type"
                  v-model="loginForm.RememberPassWord"
                />
                <a
                  @click="$notify({
                      title: '丢失密码？',
                      message: '请找群内管理员重置密码',
                })"
                  style="color: rgb(94 179 209)">忘记密码？</a>
              </div>
            </el-form-item>
            <el-form-item>
              <el-button
                style="width: 100%;height: 46px"
                type="primary"
                @click="handleLogin()"
              >登录</el-button>
            </el-form-item>
          </el-form>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { validUsername } from '@/utils/validate'
import SocialSign from './components/SocialSignin'

export default {
  name: 'Login',
  components: { SocialSign },
  data() {
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error('请输入密码！'))
      } else {
        callback()
      }
    }
    return {
      loginForm: {
        UserName:'',
        PassWord:'',
        RememberPassWord:false
      },
      loginRules: {
        UserName: [{ required: true, trigger: 'blur' , message:'请输入用户名!'}],
        PassWord: [{ required: true, trigger: 'blur', validator: validatePassword }]
      },
      passwordType: 'password',
      capsTooltip: false,
      loading: false,
      showDialog: false,
      redirect: undefined,
      otherQuery: {}
    }
  },
  watch: {
    $route: {
      handler: function(route) {
        const query = route.query
        if (query) {
          this.redirect = query.redirect
          this.otherQuery = this.getOtherQuery(query)
        }
      },
      immediate: true
    }
  },
  created() {
    // window.addEventListener('storage', this.afterQRScan)
  },
  mounted() {
    if (this.loginForm.username === '') {
      this.$refs.username.focus()
    } else if (this.loginForm.password === '') {
      this.$refs.password.focus()
    }
  },
  destroyed() {
    // window.removeEventListener('storage', this.afterQRScan)
  },
  methods: {
    checkCapslock(e) {
      const { key } = e
      this.capsTooltip = key && key.length === 1 && (key >= 'A' && key <= 'Z')
    },
    showPwd() {
      if (this.passwordType === 'password') {
        this.passwordType = ''
      } else {
        this.passwordType = 'password'
      }
      this.$nextTick(() => {
        this.$refs.password.focus()
      })
    },
    handleLogin() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loading = true
          console.log(valid)
          this.$store.dispatch('user/login', this.loginForm)
            .then(() => {
              this.$router.push({ path: this.redirect || '/', query: this.otherQuery })
              this.loading = false
            })
            .catch(() => {
              this.loading = false
            })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    getOtherQuery(query) {
      return Object.keys(query).reduce((acc, cur) => {
        if (cur !== 'redirect') {
          acc[cur] = query[cur]
        }
        return acc
      }, {})
    }
  }
}
</script>

<style scoped>
.login-container{
  min-height: 100vh;
  width: 100vw;
}
.login-container > div{
  height: 100vh;
}
.login-form-icon-container{
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}
.login-form{
  display: flex;
  flex-direction: column;
  padding-top: 72px;
}
</style>

