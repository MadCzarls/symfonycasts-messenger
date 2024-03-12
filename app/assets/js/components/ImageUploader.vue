<!--<script>-->
<!--import vue2Dropzone from 'vue2-dropzone'-->
<!--import 'vue2-dropzone/dist/vue2Dropzone.min.css'-->
<!--export default {-->
<!--  components: {-->
<!--    vueDropzone: vue2Dropzone-->
<!--  },-->
<!--  methods: {-->
<!--    onDropzoneSuccess(file, response) {-->
<!--      this.$emit('new-image', response);-->
<!--      this.$refs.vueDropzone.removeFile(file);-->
<!--    }-->
<!--  },-->
<!--  data: function () {-->
<!--    return {-->
<!--      dropzoneOptions: {-->
<!--        url: '/api/images',-->
<!--        thumbnailWidth: 150-->
<!--      }-->
<!--    }-->
<!--  }-->
<!--}-->
<!--</script>-->
<template>
  <div>
    <div class="dropzone" v-bind="getRootProps()">
      <div
          class="border"
          :class="{
          isDragActive,
        }"
      >
        <input v-bind="getInputProps()" />
        <p v-if="isDragActive">Drop the files here ...</p>
        <p v-else>Drag and drop files - or click - here to upload</p>
      </div>
    </div>
  </div>
</template>

<script>

import { useDropzone } from "vue3-dropzone";
import axios from "axios"

export default {
  setup(props, ctx) {
    const url = "/api/images";
    const saveFiles = (files) => {
      const formData = new FormData();
      formData.append("file", files[0])

      for (let x = 0; x < files.length; x++) {
        const formData = new FormData();
        formData.append("file", files[x]);

        axios
            .post(url, formData, {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            })
            .then((response) => {
              ctx.emit('new-image', response.data);
            })
            .catch((err) => {
              console.error(err); //@TODO handle
            });
      }
    };

    function onDrop(acceptFiles, rejectReasons) {
      saveFiles(acceptFiles); // saveFiles as callback
      console.log(rejectReasons); //@TODO handle
    }

    const { getRootProps, getInputProps, ...rest } = useDropzone({ onDrop });

    return {
      getRootProps,
      getInputProps,
      ...rest,
    };
  },
};
</script>

<style lang="scss" scoped>
.dropzone,
.border {
  border: 2px dashed #ccc;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  transition: all 0.3s ease;
  background: #fff;

  &.isDragActive {
    border: 2px dashed #ffb300;
    background: rgb(255 167 18 / 20%);
  }
}

</style>
