<template>
    <div class="card">
        <div class="card-header">
            Crear Tipo de Persona
        </div>
        <form action="" v-on:submit.prevent="newTipoPersona()">
            <div class="card-body">
                <tipo-persona-form :tipoPersona="tipoPersona" :errors="errors"></tipo-persona-form>
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
               tipoPersona: {
                   descripcion: ''
               },
               errors: [],
               loading:false,
               cancel_url: route('tipo-persona.index')
           }
       },
        methods: {
            newTipoPersona(){
                this.loading = true;
                axios.post(route('tipo-persona.store'), this.tipoPersona).then( (response) =>{
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