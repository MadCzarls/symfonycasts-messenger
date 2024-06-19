import 'bootstrap/dist/css/bootstrap.css';
require('./bootstrap');

import { createApp } from 'vue';
import ImageApp from './components/ImageApp';

const app = createApp(ImageApp)
app.component('image-app', ImageApp);
app.mount("#images-app")