<?php
class eiformulario extends gestionescuelas_ei_formulario
{
	function extender_objeto_js()
	{
		$importe_mensual_cuota_x_grado = dao_consultas::catalogo_de_parametros("importe_mensual_cuota_x_grado");
		$ingresa_importe_en_generacion_cargos = dao_consultas::catalogo_de_parametros("ingresa_importe_en_generacion_cargos");
        $cant_cuotas_cobro_inscripcion = dao_consultas::catalogo_de_parametros("cant_cuotas_cobro_inscripcion");

		echo "
		{$this->objeto_js}.ini = function()
		{
			var importe_mensual_cuota_x_grado = '{$importe_mensual_cuota_x_grado}';
			var ingresa_importe_en_generacion_cargos = '{$ingresa_importe_en_generacion_cargos}';
			
			if (ingresa_importe_en_generacion_cargos == 'SI') {
			    this.ef('importe_cuota').mostrar();
			    this.ef('importe_cuota').set_obligatorio(1);
			} else {
				this.ef('importe_cuota').ocultar();
			}
			
			if (importe_mensual_cuota_x_grado == 'SI') {
				this.ef('grado').mostrar();
			} else {
				this.ef('grado').ocultar();
			}
			
			this.ef('id_persona').ocultar();
			this.ef('cuota').ocultar();
			this.ef('numero_cuota').ocultar();
			this.ef('numero_cuota_inscripcion').ocultar();
			this.ef('cargo_a_generar').set_obligatorio(1);
		}
		
		//---- Procesamiento de EFs --------------------------------
		{$this->objeto_js}.evt__forma_generacion__procesar = function(es_inicial)
		{
			if (!es_inicial) {
			    if (this.ef('forma_generacion').get_estado() == 'G') {
			        alert('Tenga en cuenta que al seleccionar la opción Grupal se le generará el costo a todos los alumnos activos cargados en el sistema');
			        this.ef('id_persona').set_obligatorio(0);
			        this.ef('id_persona').resetear_estado();
			        this.ef('id_persona').ocultar();
			    } else {
			        this.ef('id_persona').mostrar();
			        this.ef('id_persona').set_obligatorio(1);
			    }
			}
		}
		
		{$this->objeto_js}.evt__cargo_a_generar__procesar = function(es_inicial)
		{
		    this.ef('anio').resetear_estado();
		    
		    var cant_cuotas_cobro_inscripcion = '{$cant_cuotas_cobro_inscripcion}';
		    var ingresa_importe_en_generacion_cargos = '{$ingresa_importe_en_generacion_cargos}';
		    
		    switch(this.ef('cargo_a_generar').get_estado())
			{
				case '1': //inscripción anual
				    this.ef('anio').set_obligatorio(1);
				    this.ef('cuota').set_obligatorio(0);
				    this.ef('cuota').resetear_estado();
			        this.ef('cuota').ocultar();
			        this.ef('numero_cuota').set_obligatorio(0);
			        this.ef('numero_cuota').resetear_estado();
			        this.ef('numero_cuota').ocultar();
			        
			        if (cant_cuotas_cobro_inscripcion > 1) {
			            this.ef('numero_cuota_inscripcion').mostrar();
			            this.ef('numero_cuota_inscripcion').set_obligatorio(1);
			        }
			        
			        if ((cant_cuotas_cobro_inscripcion > 1) && (ingresa_importe_en_generacion_cargos == 'SI')) {
			            alert('Tenga en cuenta que los valores de las cuotas que va a generar para las inscripciones NO saldrán del importe ingresado por pantalla, sino que se tomará de los parámetros correspondientes:".'\n'." * importe_cuota_uno_nivel_inicial ".'\n'." *importe_cuota_dos_nivel_inicial ".'\n'." *importe_cuota_uno_nivel_primario ".'\n'." *importe_cuota_dos_nivel_primario".'\n'."Se recomienda cambiar a NO el valor del parámetro ingresa_importe_en_generacion_cargos".'\n'."'); 
			        }
			        break;
				case '2': //cuota mensual
				    this.ef('cuota').mostrar();
			        this.ef('cuota').set_obligatorio(1);
			        this.ef('anio').set_obligatorio(1);
			        this.ef('numero_cuota').set_obligatorio(0);
			        this.ef('numero_cuota').resetear_estado();
			        this.ef('numero_cuota').ocultar();
			        this.ef('numero_cuota_inscripcion').set_obligatorio(0);
			        this.ef('numero_cuota_inscripcion').resetear_estado();
			        this.ef('numero_cuota_inscripcion').ocultar();
			        break;
			    case '3': //materiales
			        this.ef('cuota').resetear_estado();
			        this.ef('cuota').ocultar();
			        this.ef('numero_cuota').mostrar();
			        this.ef('numero_cuota').set_obligatorio(1);
			        this.ef('anio').set_obligatorio(1);
			        if (this.ef('forma_generacion').get_estado() == 'G') {
			            alert('Tenga en cuenta que los cargos de los materiales solo se les generará a los alumnos del nivel inicial (Sala de 4 y sala de 5).');    
			        }
			        break;    
			}
		}
		";
	}
}
?>