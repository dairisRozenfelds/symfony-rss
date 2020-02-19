<script>
    export default {
        name: "FormComponent",
        props: {
            csrfToken: {
                type: String,
                required: true
            },
            submitRoute: {
                type: String,
                required: true
            }
        }
    }
</script>
