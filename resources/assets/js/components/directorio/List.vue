<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Nombre
                        </th>
                        <th>
                            Dirección
                        </th>
                        <th>
                            Telefono
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(directorio, index) in directorios">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{directorio.nombre}}
                        </td>
                        <td>
                            {{directorio.direccion}}
                        </td>
                        <td>
                            {{directorio.telefono}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(directorio.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteDirectorio(directorio)">
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
                directorios: []
            }
        },
        mounted() {
            this.getNivelesAgua();
        },
        methods: {

            deleteDirectorio(directorio){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('directorio.destroy', {id: directorio.id})).then((response)=>{
                        if(response.data.success){
                            this.getNivelesAgua();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getNivelesAgua(){
                axios.get(route('directorio.all')).then((response) => {
                    this.directorios = response.data.directorios;
                });
            },
            getEdiUrl(id){
                return route('directorio.edit', {id: id})
            }

        }
    }
</script>