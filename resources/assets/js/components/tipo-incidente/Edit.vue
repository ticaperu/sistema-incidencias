<template>
    <div class="card">
        <div class="card-header">
            Editar Tipo Incidente
        </div>
        <form action="" v-on:submit.prevent="editTipoIncidente()">
            <div class="card-body">
                <tipo-incidente-form :tipoIncidente="tipoIncidente" :errors="errors"></tipo-incidente-form>
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
                tipoIncidente: {
                    descripcion: ''
                },
                errors: [],
                loading:false,
                cancel_url:route('tipo-incidente.index')
            }
        },
        mounted(){
            let id = this.getIDfromURL();

            axios.get(route('tipo-incidente.show', {id:id})).then((response) => {
                this.tipoIncidente = response.data.tipoIncidente;
            });

        },
        methods: {
            editTipoIncidente(){
                this.loading = true;
                axios.put(route('tipo-incidente.update',{id:this.tipoIncidente.id} ), this.tipoIncidente).then( (response) =>{
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
            },
            getIDfromURL(){
                let path = window.location.pathname;
                let segments = path.split("/");
                let id = null;

                for(let segment of segments)
                {
                    if(!isNaN(segment))
                    {
                        if(!id)
                        {
                            id = segment;
                        }
                    }
                }

                return id;
            }
        }
    }
</script>