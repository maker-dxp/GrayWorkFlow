<template>
  <Photo :PictureArr="plot"></Photo>
</template>

<script>
import Photo from '@/components/photo-graw'

export default {
  name: "test",
  components:{
    Photo
  },
  data(){
    return{
      plot:[
        {
          title:'hello world',
          info:'你好世界！',
          picture:require('@/assets/login_background.png')
        },
        {
          title:'你好！',
          info:'你好世界！',
          picture:require('@/assets/icon.jpg')
        }
      ]
    }
  }
}
</script>

<style scoped>

</style>
