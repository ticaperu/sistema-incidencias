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
                    <tr v-for="(tipoIncidente, index) in tiposIncidentes">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{tipoIncidente.descripcion}}
                        </td>
                        <td>
                            <a :href="getEditUrl(tipoIncidente.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoIncidente(tipoIncidente)">
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
                tiposIncidentes: []
            }
        },
        mounted() {
            this.getTiposIncidentes();
        },
        methods: {

            deleteTipoIncidente(tipoIncidente){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('tipo-incidente.destroy',{id:tipoIncidente.id})).then((response)=>{
                        if(response.data.success){
                            this.getTiposIncidentes();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getTiposIncidentes(){
                axios.get(route('tipo-incidente.all')).then((response) => {
                    this.tiposIncidentes = response.data.tiposIncidentes;
                });
            },
            getEditUrl(id){
                return route('tipo-incidente.edit', {id:id});
            }

        }
    }
</script>