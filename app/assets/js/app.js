// import * as Vue from 'vue'
import 'bootstrap/dist/css/bootstrap.css';
//
// Vue.component('image-app', ImageApp);
//
// const app = new Vue({
//     el: '#images-app'
// });

require('./bootstrap');
import { createApp } from 'vue';

// import components from  './components/';
import ImageApp from './components/ImageApp';

let app=createApp(ImageApp)
// app.use(components)


app.mount("#app")