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
                        <th>
                           Estado
                        </th>
                        <th>
                            Puntaje
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(actividadPuntuacion, index) in actividadesPuntuacion">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{actividadPuntuacion.descripcion}}
                        </td>
                        <td>
                            {{actividadPuntuacion.estado_incidente.descripcion}}
                        </td>
                        <td>
                            {{actividadPuntuacion.puntaje}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(actividadPuntuacion.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteActividadPuntuacion(actividadPuntuacion)">
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
                actividadesPuntuacion: []
            }
        },
        mounted() {
            this.getNivelesAgua();
        },
        methods: {

            deleteActividadPuntuacion(actividadPuntuacion){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('actividad-puntuacion.destroy', {id: actividadPuntuacion.id})).then((response)=>{
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
                axios.get(route('actividad-puntuacion.all')).then((response) => {
                    this.actividadesPuntuacion = response.data.actividadesPuntuacion;
                });
            },
            getEdiUrl(id){
                return route('actividad-puntuacion.edit', {id: id})
            }

        }
    }
</script>