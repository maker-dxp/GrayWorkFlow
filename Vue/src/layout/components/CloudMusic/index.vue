<template>
  <div ref="CloudMusic" class="CloudMusic">
    <div class="CloudMusic-Head">
      <div class="close control-button">
        x
      </div>
      <div
        ref="move"
        v-on:mousedown="startMove"
        class="move control-button"
      >
        +
      </div>
    </div>
    <video style="width: 100%;height: 60px" controls="" autoplay="" name="media">
      <source src="http://blog.feibam.club/audio/EndlessJourney.mp3" type="audio/mp3">
    </video>
  </div>
</template>

<script>
export default {
  name: "CloudMusic",
  data(){
    return {
      top:0,
      left:0
    }
  },
  methods:{
    startMove(){
      this.$refs.move.style.cursor = 'cell'
      document.documentElement.addEventListener('mousemove',this.onMove)
      document.documentElement.addEventListener('mouseup',this.endMove)
    },
    onMove(e){
      const el = this.$refs.CloudMusic
      const move_el = this.$refs.move
      const mouse_x = e.clientX
      const mouse_y = e.clientY
      const current_x = mouse_x - move_el.offsetLeft
      const current_y = mouse_y - move_el.offsetTop
      el.style.left = `${current_x}px`
      el.style.top = `${current_y}px`
    },
    endMove(e){
      this.$refs.move.style.cursor = 'pointer'
      document.documentElement.removeEventListener('mousemove',this.onMove)
    }
  }
}
</script>

<style lang="scss" scoped>
  .CloudMusic{
    position: absolute;
    border-radius: 16px;
    width: 243px;
    height: 102px;
    left: 0;
    top: 0;
    background-color: #fff;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 10%), 0 2px 4px 0 rgb(0 0 0 / 10%);
  }
  .CloudMusic-Head{
    display: flex;
    flex-direction: row-reverse;
    height: 18px;
    width: 100%;
    .control-button{
      display: flex;
      background-color: #4fcaff;
      align-items: center;
      justify-content: center;
      transition: all .25s ease;
      cursor: pointer;
      width: 32px;
      height: 20px;
      border-radius: 16px;
    }
    .close{
      color: black;
    }
    .close:hover{
      background-color: red;
      color: white;
    }
    .move{
      cursor: pointer;
    }
  }
</style>
