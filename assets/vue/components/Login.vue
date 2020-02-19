<template>
    <div class="login-wrapper">
        <div class="login-form">
            <h1 class="form-title">Please sign in</h1>
            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input type="text" name="email" id="inputEmail" autofocus v-model="email">
            </div>
           <div class="form-group">
               <label for="inputPassword" v-model="password">Password</label>
               <input type="password" name="password" id="inputPassword">
           </div>
            <div class="actions">
                <button class="btn btn-primary" type="submit" id="buttonSubmitLogin">Sign in</button>
            </div>
        </div>
    </div>
</template>

<script>
    import FormComponent from './base/FormComponent';

    export default {
        name: "Login",
        extends: FormComponent,
        props: {
            csrfToken: {
                type: String,
                required: true
            },
            registerRoute: {
                type: String,
                required: true
            },
            loginSubmitRoute: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                email: null,
                password: null
            }
        },

        methods: {

        }
    }
</script>
