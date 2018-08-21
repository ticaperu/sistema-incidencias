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
                    <tr v-for="(estadoIncidente, index) in estadosIncidentes">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{estadoIncidente.descripcion}}
                        </td>
                        <td>
                            <a :href="getEditUrl(estadoIncidente.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoPersona(estadoIncidente)">
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
                estadosIncidentes: []
            }
        },
        mounted() {
            this.getTiposPersonas();
        },
        methods: {

            deleteTipoPersona(estadoIncidente){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('estado-incidente.destroy', {id: estadoIncidente.id})).then((response)=>{
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
                axios.get(route('estado-incidente.all')).then((response) => {
                    this.estadosIncidentes = response.data.estadosIncidentes;
                });
            },
            getEditUrl(id){
                return route('estado-incidente.edit', {id: id});
            }

        }
    }
</script>