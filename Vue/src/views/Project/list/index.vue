<template>
  <div class="default-layout">
    <el-table
      :data="tableData"
    >
      <el-table-column
        prop="ProjectType"
        label="项目类别"
      />
      <el-table-column
        prop="ProjectName"
        label="项目名称"
      />
      <el-table-column
        prop="Project_CreateAt"
        label="项目创建时间"
      />
      <el-table-column
        label="项目人员状态"
      >
        <template slot-scope="scope">
          <el-tag>翻译</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        label="操作"
      >
        <template>
          <el-button type="text" size="small">详情</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
export default {
  name: "project_list",
  data() {
    return {
      Professions: [],
      tableData: [
        {
          id: 0,
          ProjectType: '全力回避flag酱',
          ProjectName: '变成垃圾',
          Project_CreateAt: '2021-07-28',
          ProjectStaff: [
          ]
        }
      ]
    }
  },
  methods: {

  },
  mounted() {

  },
}
</script>

<style scoped>

</style>
