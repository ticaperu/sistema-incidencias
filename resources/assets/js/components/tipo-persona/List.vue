<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Descripción
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(tipoPersona, index) in tiposPersonas">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{tipoPersona.descripcion}}
                        </td>
                        <td>
                            <a :href="getEditUrl(tipoPersona.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoPersona(tipoPersona)">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                tiposPersonas: []
            }
        },
        mounted() {
            this.getTiposPersonas();
        },
        methods: {

            deleteTipoPersona(tipoPersona){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('tipo-persona.destroy', tipoPersona.id)).then((response)=>{
                        if(response.data.success){
                            this.getTiposPersonas();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getTiposPersonas(){
                axios.get(route('tipo-persona.all')).then((response) => {
                    this.tiposPersonas = response.data.tiposPersonas;
                });
            },
            getEditUrl(id){
                return route('tipo-persona.edit', {id:id});
            }

        }
    }
</script>