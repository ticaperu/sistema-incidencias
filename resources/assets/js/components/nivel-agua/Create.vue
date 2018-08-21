<template>
    <div class="card">
        <div class="card-header">
            Crear Nivel de Agua
        </div>
        <form action="" v-on:submit.prevent="newNivelAgua()">
            <div class="card-body">
                <nivel-agua-form :nivelAgua="nivelAgua" :errors="errors"></nivel-agua-form>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit" :disabled="loading">
                    Guardar
                </button>
                <a class="btn btn-danger" :href="cancel_url">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
       data(){
           return {
               nivelAgua: {
                   descripcion: ''
               },
               errors: [],
               loading:false,
               cancel_url: route('nivel-agua.index')
           }
       },
        methods: {
            newNivelAgua(){
                this.loading = true;
                axios.post(route('nivel-agua.store'), this.nivelAgua).then( (response) =>{
                    if(response.data.success)
                    {
                        window.location.href = this.cancel_url;
                    }

                    this.loading = false;

                }).catch((error) =>{
                    if(error.response.status == 422)
                    {
                        this.errors = error.response.data.errors;
                    }

                    this.loading = false;

                })
            }
        }
    }
</script>