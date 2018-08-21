<template>
    <div class="card">
        <div class="card-header">
            Crear Estado Incidente
        </div>
        <form action="" v-on:submit.prevent="newEstadoIncidente()">
            <div class="card-body">
                <estado-incidente-form :estadoIncidente="estadoIncidente" :errors="errors"></estado-incidente-form>
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
               estadoIncidente: {
                   descripcion: ''
               },
               errors: [],
               loading:false,
               cancel_url: route('estado-incidente.index')
           }
       },
        methods: {
            newEstadoIncidente(){
                this.loading = true;
                axios.post(route('estado-incidente.store'), this.estadoIncidente).then( (response) =>{
                    if(response.data.success)
                    {
                        window.location.href = route('estado-incidente.index');
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