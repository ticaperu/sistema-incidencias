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
                            <a href="#" class="btn btn-primary" v-on:click="setPersona(persona)" data-toggle="modal"
                               data-target="#modalState">
                                Validar
                            </a>
                        </td>
                    </tr>
                </thead>
            </table>

            <div class="modal fade" id="modalState" tabindex="-1" role="dialog" aria-labelledby="modalState"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalStateLabel">Cambiar de Estado</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="state">
                                            Estado
                                        </label>
                                        <select name="state" id="state" v-model="persona.state" class="form-control">
                                            <option value="">Selecciona</option>
                                            <option value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" :disabled="loading"
                                    v-on:click="updatePersona()">Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                personas: [],
                persona:{},
                loading:false
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
                axios.get(route('persona.inactives')).then((response) => {
                    this.personas = response.data.personas;
                });
            },
            getEditUrl(id){
                return route('persona.edit', {id:id});
            },
            setPersona(persona){
                this.persona = persona;
            },
            updatePersona(){
                this.loading = true;
                axios.put(route('persona.activepersona', {id:this.persona.id}), this.persona).then( (response) =>{
                    
                    if(response.data.success)
                    {
                        this.getPersonas();
                        $('#modalState').modal('hide');
                        alert(response.data.message);
                        
                    }
                    else
                    {
                        alert(response.data.message);
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