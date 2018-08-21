<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                <tr>
                    <th class="number-col">#</th>
                    <th>
                        Fecha
                    </th>
                    <th>
                        Territorio Vecinal
                    </th>
                    <th>
                        Urbanización
                    </th>
                    <th>
                        Descripción
                    </th>
                    <th>
                        Estado
                    </th>
                    <th>

                    </th>
                </tr>
                <tr v-for="(incidente, index) in incidentes">
                    <td>
                        {{index + 1}}
                    </td>
                    <td>
                        {{incidente.fecha}}
                    </td>
                    <td>
                        {{incidente.urbanizacion.territorio_vecinal.descripcion}}
                    </td>
                    <td>
                        {{incidente.urbanizacion.descripcion}}
                    </td>
                    <td>
                        {{incidente.descripcion}}
                    </td>
                    <td >
                        <span class="badge badge-secondary" :style="'background: ' + incidente.estado_incidente.color">{{incidente.estado_incidente.descripcion}}</span>
                    </td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#mapModal" class="btn btn-primary"
                           v-on:click="setMap(incidente)">
                            Ubicación
                        </a>
                        <a :href="getEditUrl(incidente.id)" class="btn btn-primary" >
                            Detalle
                        </a>
                        <a href="#" class="btn btn-primary" v-on:click="setMap(incidente)" data-toggle="modal"
                           data-target="#modalState">
                            Estado
                        </a>
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalCoordinacion"
                           v-on:click="setMap(incidente)">
                            Registrar Coordinación
                        </a>
                    </td>
                </tr>
                </thead>
            </table>
        </div>

        <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Mapa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="map" style="height: 250px; width: 100%">

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <br>
                                <br>
                                <a href="#" class="btn btn-success" v-on:click="addPolyline()">Agregar Linea</a>
                                <br>
                                <br>
                                <ul class="list-group">
                                    <li class="list-group-item" v-for="(polyline, index) in polylines">
                                        <div class="row">
                                            <div class="col-sm-1">
                                                <a href="#">
                                                    <i class="fas fa-circle" :style="'color:'+ polyline.color"></i>
                                                </a>
                                            </div>
                                            <div class="col-sm-10">
                                                <textarea name="line_descripcion" id="line_descripcion" rows="1" v-model="polyline.descripcion"
                                                          class="form-control"></textarea>
                                            </div>
                                            <div class="col-sm-1">
                                                <a href="#" class="text-danger float-right"
                                                   v-on:click="removeLine(polyline, index)">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" :disabled="loading"
                                v-on:click="updateIncidente()">Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
                                    <label for="estado_incidente_id">Estado</label>
                                    <select name="estado_incidente_id" id="estado_incidente_id" class="form-control"
                                            v-model="selectedIncidente.estado_incidente_id">
                                        <option value="">Selecione..</option>
                                        <option v-for="estadoIncidente in estadosIncidentes"
                                                :value="estadoIncidente.id">
                                            {{estadoIncidente.descripcion}}
                                        </option>
                                    </select>
                                    <small class="form-text text-danger" v-if="errors.estado_incidente_id"
                                           v-for="error in errors.estado_incidente_id">
                                        {{error}}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" :disabled="loading"
                                v-on:click="updateIncidente()">Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCoordinacion" tabindex="-1" role="dialog" aria-labelledby="modalCoordinacion"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCoordinacionLabel">Registrar Coordinaciones</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="estado_incidente_id">Coordinaciones</label>
                                    <select name="coordinacion_id" id="coordinacion_id" class="form-control"
                                            v-model="coordinacion_id">
                                        <option value="">Selecione..</option>
                                        <option v-for="coordinacion in coordinacions" :value="coordinacion.id">
                                            {{coordinacion.descripcion}}
                                        </option>
                                    </select>
                                    <small class="form-text text-danger" v-if="errors.coordinacion_id"
                                           v-for="error in errors.coordinacion_id">
                                        {{error}}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" rows="3" v-model="atencion_descripcion"
                                              class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" :disabled="loading"
                                v-on:click="registrarCoordinacion()">Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                incidentes: [],
                map: null,
                marker: null,
                selectedIncidente: {},
                polgygon: null,
                polylines: [],
                loading: false,
                estadosIncidentes: [],
                coordinacions: [],
                errors: [],
                coordinacion_id: '',
                atencion_descripcion: '',
                infoWindow: null
            }
        },
        mounted() {
            axios.get(route('estado-incidente.all')).then((response) => {
                this.estadosIncidentes = response.data.estadosIncidentes;
            });

            axios.get(route('coordinacion.all')).then((response) => {
                this.coordinacions = response.data.coordinacions;
            });

            this.getPersonas();

            this.infoWindow = new google.maps.InfoWindow({
                content: ''
            });

            $('#mapModal').on('shown.bs.modal', () => {

                this.polylines = [];

                this.map = new GMaps({
                    div: '#map',
                    zoom: 17,
                    lat: this.selectedIncidente.latitud ? this.selectedIncidente.latitud : -12.043333,
                    lng: this.selectedIncidente.latitud ? this.selectedIncidente.longitud : -77.028333
                });


                this.marker = this.map.addMarker({
                    lat: this.selectedIncidente.latitud ? this.selectedIncidente.latitud : -12.043333,
                    lng: this.selectedIncidente.longitud ? this.selectedIncidente.longitud : -77.028333,
                    draggable: true,
                    animation: google.maps.Animation.DROP
                });

                this.setMapArea();

                this.marker.addListener('dragend', (event) => {

                    let isWithinPolygon = google.maps.geometry.poly.containsLocation(event.latLng, this.polygon);

                    if (isWithinPolygon) {
                        this.map.setCenter(this.marker.getPosition().lat(), this.marker.getPosition().lng());
                        let myLatlng = new google.maps.LatLng(this.map.getCenter().lat(), this.map.getCenter().lng());
                        this.marker.setPosition(myLatlng);
                    } else {
                        let myLatlng = new google.maps.LatLng(this.selectedIncidente.urbanizacion.latitude, this.selectedIncidente.urbanizacion.longitude);
                        this.map.setCenter(this.selectedIncidente.urbanizacion.latitude, this.selectedIncidente.urbanizacion.longitude);
                        this.marker.setPosition(myLatlng);
                    }

                    this.selectedIncidente.latitud = this.marker.getPosition().lat();
                    this.selectedIncidente.longitud = this.marker.getPosition().lng();

                });

                for (let polyline of this.selectedIncidente.polylines) {
                    let g_polyline = this.map.drawPolyline({
                        path: polyline.coordinates,
                        strokeColor: 'blue',
                        strokeOpacity: 1,
                        strokeWeight: 6,
                        editable: true
                    });

                    let new_data = {color: 'blue', polyline: g_polyline, descripcion: polyline.descripcion};

                    this.createInfoWindow(new_data);

                    this.polylines.push(new_data);

                }

            });

        },
        methods: {
            deleteTipoPersona(incidente) {
                let r = confirm('Estas seguro de eliminar este registro');

                if (r === true) {
                    axios.delete(route('incidente.destroy', {id: incidente.id})).then((response) => {
                        if (response.data.success) {
                            this.getPersonas();
                        } else {
                            alert(response.data.message);
                        }
                    }).catch((error) => {
                        console.log(error.data);
                    });
                }
            },
            getPersonas() {
                axios.get(route('incidente.attentions')).then((response) => {
                    this.incidentes = response.data.incidentes;
                });
            },
            getEditUrl(id) {
                return route('incidente.detalleatencion', {id: id});
            },
            setMap(row) {
                this.coordinacion_id = '';
                this.atencion_descripcion = '';
                this.selectedIncidente = row;
            },
            setMapArea() {
                if (this.polygon) {
                    this.polygon.setMap(null);
                }

                this.polygon = this.map.drawPolygon({
                    paths: this.selectedIncidente.urbanizacion.coordenadas,
                    strokeColor: '#e9e513',
                    strokeOpacity: 1,
                    strokeWeight: 3,
                    fillColor: '#da151b',
                    fillOpacity: 0.6
                });
            },
            getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            },
            addPolyline() {
                let path = [
                    this.selectedIncidente.urbanizacion.coordenadas[0],
                    this.selectedIncidente.urbanizacion.coordenadas[1]
                ];

                let color = this.getRandomColor();

                let polyline = this.map.drawPolyline({
                    path: path,
                    strokeColor: 'blue',
                    strokeOpacity: 1,
                    strokeWeight: 6,
                    editable: true
                });

                let new_data = {color: 'blue', polyline: polyline, descripcion: ''};

                this.createInfoWindow(new_data);

                this.polylines.push(new_data);
            },
            updateIncidente() {
                let polylines = [];

                for (let polyline of this.polylines) {
                    let len = polyline.polyline.getPath().getLength();
                    let coordinates = "";
                    for (var i = 0; i < len; i++) {
                        if (i == (len - 1)) {
                            coordinates += polyline.polyline.getPath().getAt(i).toUrlValue(5);
                        } else {
                            coordinates += polyline.polyline.getPath().getAt(i).toUrlValue(5) + ";";
                        }
                    }

                    polylines.push({coordinates:coordinates, descripcion:polyline.descripcion});
                }

                this.selectedIncidente.polylines = polylines;

                this.loading = true;
                axios.put(route('incidente.update', {id: this.selectedIncidente.id}), this.selectedIncidente).then((response) => {
                    $('#mapModal').modal('hide');
                    $('#modalState').modal('hide');
                    this.getPersonas();
                    this.loading = false;
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                    this.loading = false;
                })
            },
            removeLine(polyline, index) {
                polyline.polyline.setMap(null);
                this.polylines.splice(index, 1);
            },
            registrarCoordinacion() {
                this.loading = true;
                axios.post(route('incidente.registrar-coordinacion', {id: this.selectedIncidente.id}),
                    {
                        coordinacion_id: this.coordinacion_id,
                        descripcion: this.atencion_descripcion
                    }
                ).then((response) => {
                    $('#modalCoordinacion').modal('hide');
                    this.loading = false;
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                    this.loading = false;
                })
            },
            createInfoWindow(poly) {
                poly.polyline.addListener('click', (event) => {
                    // infowindow.content = content;
                    this.infoWindow.setContent(poly.descripcion);
                    // infowindow.position = event.latLng;
                    this.infoWindow .setPosition(event.latLng);
                    this.infoWindow .open(this.map.map);
                });
            }
        }
    }
</script>