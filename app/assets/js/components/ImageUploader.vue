<!--<template>-->
<!--  <div>-->
<!--    <vue-dropzone-->
<!--        id="dropzone"-->
<!--        ref="vueDropzone"-->
<!--        v-on:vdropzone-success="onDropzoneSuccess"-->
<!--        :options="dropzoneOptions"-->
<!--    ></vue-dropzone>-->
<!--  </div>-->
<!--</template>-->

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
    <div v-bind="getRootProps()">
      <input v-bind="getInputProps()" />
      <p v-if="isDragActive">Drop the files here ...</p>
      <p v-else>Drag 'n' drop some files here, or click to select files</p>
    </div>
    <button @click="open">open</button>
  </div>
</template>

<script>

import { useDropzone } from "vue3-dropzone";
import axios from "axios"

export default {
  name: "UseDropzoneDemo",
  setup() {
    const url = "/api/images";
    const saveFiles = (files) => {
      const formData = new FormData();
      formData.append("file", files[0])
      for (var x = 0; x < files.length; x++) {
        const formData = new FormData();
        formData.append("file", files[x]);

        axios
            .post(url, formData, {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            })
            .then((response) => {
              console.info(response.data);
            })
            .catch((err) => {
              console.error(err);
            });
      }

      // axios
      //     .post(url, formData, {
      //       headers: {
      //         "Content-Type": "multipart/form-data",
      //       },
      //     })
      //     .then((response) => {
      //       console.info(response.data);
      //     })
      //     .catch((err) => {
      //       console.error(err);
      //     });

    };

    function onDrop(acceptFiles, rejectReasons) {
      saveFiles(acceptFiles); // saveFiles as callback
      console.log(rejectReasons);
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