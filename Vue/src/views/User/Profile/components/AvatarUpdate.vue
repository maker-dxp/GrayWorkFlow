<template>
  <div>
    <div
      class="out-box"
      ref="dropBox"
      v-on:click="$refs.dropBox.children.fileInput.click()"
      v-on:dragover="ondragover"
      v-on:drop="ondrop"
    >
      <i class="el-icon-upload"></i>
      <a>拖拽上传,或者  <span style="color: #409eff">点击上传</span></a>
      <input ref="fileInput" v-on:input="openInput" style="display: none" name="fileInput" type="file"></input>
    </div>
    <div style="display: flex;justify-content: center;margin-top: 20px" v-if="Files.length > 0">
      <el-button>上传</el-button>
      <el-button>清空文件</el-button>
    </div>
  </div>
</template>

<script>
export default {
  name: "upload-avatar",
  data(){
    return{
      Files:[]
    }
  },
  mounted() {
  },
  methods:{
    onclick:function (e){
      console.log(e)
    },
    openInput:function (el){
      console.log(this.Files)
      this.Files.push(this.$refs.fileInput.files[0])
    },
    ondragover:function (e){
      e.preventDefault();
    },
    ondrop(e){
      e.preventDefault();
      const file = e.dataTransfer.files
      console.log(file)
    }
  },
}
</script>

<style scoped>
.out-box{
  display: flex;
  flex-flow: column;
  justify-content: center;
  align-items: center;
  max-width: 384px;
  width: 100%;
  height: 256px;
  margin: auto;
  border: 2px dashed #d9d9d9;
  border-radius: 5%;
  cursor: pointer;
}
.out-box .el-icon-upload{
  font-size: 67px;
  color: #c0c4cc;
}
.out-box:hover{
  border: 2px dashed #a6f1ff;
}
</style>
