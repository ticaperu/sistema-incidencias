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
                            Tipo Persona
                        </th>
                        <th>
                            DNI
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(persona, index) in personas">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{persona.nombres}}
                        </td>
                        <td>
                            {{persona.tipopersona.descripcion}}
                        </td>
                        <td>
                            {{persona.dni}}
                        </td>
                        <td>
                            <a :href="getEditUrl(persona.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoPersona(persona)">
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
                personas: []
            }
        },
        mounted() {
            this.getPersonas();
        },
        methods: {

            deleteTipoPersona(persona){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('persona.destroy', {id:persona.id})).then((response)=>{
                        if(response.data.success){
                            this.getPersonas();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getPersonas(){
                axios.get(route('persona.all')).then((response) => {
                    this.personas = response.data.personas;
                });
            },
            getEditUrl(id){
                return route('persona.edit', {id:id});
            }
        }
    }
</script>