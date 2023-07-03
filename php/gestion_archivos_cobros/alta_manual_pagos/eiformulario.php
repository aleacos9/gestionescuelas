<?php
class eiformulario extends gestionescuelas_ei_formulario
{
    function extender_objeto_js()
    {
        echo "
        
        {$this->objeto_js}.ini = function()
		{
			this.ef('id_marca_tarjeta').ocultar();
			//this.ef('concepto').ocultar();
			this.ef('fecha_pago').ocultar();
			this.ef('numero_comprobante').ocultar();
			this.ef('numero_lote').ocultar();
			this.ef('numero_autorizacion').ocultar();
			this.ef('importe').ocultar();
			
			this.ef('id_medio_pago').set_obligatorio(1);
		}
        
        //---- Procesamiento de EFs --------------------------------
		{$this->objeto_js}.evt__id_medio_pago__procesar = function(es_inicial)
		{
		    this.ef('id_marca_tarjeta').ocultar();
			this.ef('fecha_pago').ocultar();
			this.ef('numero_comprobante').ocultar();
			this.ef('numero_lote').ocultar();
			this.ef('numero_autorizacion').ocultar();
			this.ef('importe').ocultar();
		    
		    switch(this.ef('id_medio_pago').get_estado())
			{
				case '2': //transferencia
				    this.ef('id_marca_tarjeta').resetear_estado();
				    this.ef('id_marca_tarjeta').set_obligatorio(0);
				    this.ef('fecha_pago').resetear_estado();
				    this.ef('numero_comprobante').resetear_estado();
				    this.ef('numero_lote').resetear_estado();
				    this.ef('numero_autorizacion').resetear_estado();
				    this.ef('importe').resetear_estado();
				    
				    this.ef('fecha_pago').mostrar();
				    this.ef('numero_comprobante').mostrar();
				    this.ef('importe').mostrar();
				    this.ef('concepto').set_obligatorio(1);
				    this.ef('fecha_pago').set_obligatorio(1);
				    this.ef('numero_comprobante').set_obligatorio(1);
				    this.ef('importe').set_obligatorio(1);
				    break;
				case '5': //posnet
				    this.ef('fecha_pago').resetear_estado();
				    this.ef('numero_comprobante').resetear_estado();
				    this.ef('importe').resetear_estado();
				
				    this.ef('id_marca_tarjeta').mostrar();
				    this.ef('fecha_pago').mostrar();
				    this.ef('numero_comprobante').mostrar();
				    this.ef('numero_lote').mostrar();
				    this.ef('numero_autorizacion').mostrar();
				    this.ef('importe').mostrar();
				    
				    this.ef('id_marca_tarjeta').set_obligatorio(1);
				    this.ef('concepto').set_obligatorio(1);
				    this.ef('fecha_pago').set_obligatorio(1);
				    this.ef('numero_comprobante').set_obligatorio(1);
				    this.ef('importe').set_obligatorio(1);
				    break;
				case '6': //depósito
				    this.ef('id_marca_tarjeta').resetear_estado();
				    this.ef('id_marca_tarjeta').set_obligatorio(0);
				    this.ef('fecha_pago').resetear_estado();
				    this.ef('numero_comprobante').resetear_estado();
				    this.ef('numero_lote').resetear_estado();
				    this.ef('numero_autorizacion').resetear_estado();
				    this.ef('importe').resetear_estado();
				
				    this.ef('fecha_pago').mostrar();
				    this.ef('numero_comprobante').mostrar();
				    this.ef('importe').mostrar();
				    this.ef('concepto').set_obligatorio(1);
				    this.ef('fecha_pago').set_obligatorio(1);
				    this.ef('numero_comprobante').set_obligatorio(1);
				    this.ef('importe').set_obligatorio(1);
			}     
		}
		";
    }
}



