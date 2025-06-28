     <template>
        <ckeditor :editor="editor" v-model="model" :config="editorConfig"></ckeditor>
    </template>


<script setup>

const model = defineModel({
    type: String,
    required: true,
});


</script>
<script>
import * as CKEditor from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';


export default {
    components: {
        ckeditor: CKEditor.component
    },
    data() {
        return {
            editor: ClassicEditor,
            editorData: '<p>Initial content of the editor.</p>',
            editorConfig: {
                licenseKey: 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3NTIyNzgzOTksImp0aSI6IjcxY2ZkOGNiLTAwZjQtNDU5ZS04N2E2LTZkYjM1NDQ0Mjc4YSIsInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiLCJzaCJdLCJ3aGl0ZUxhYmVsIjp0cnVlLCJsaWNlbnNlVHlwZSI6InRyaWFsIiwiZmVhdHVyZXMiOlsiKiJdLCJ2YyI6IjM1ODc2MzQ5In0.HBK2M5tikAPBn3xGe_TvATopOifcYDf16NWjd7ZvtxLEZTg23kjIOJlEHNxRv7_w-Gr1ed9e-Wjhzuv_gCSRRA'
                // The configuration of the editor.
            },
            toolbar: ['heading', '|', 'bold', 'italic']
        };
    }
}
</script>
