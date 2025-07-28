<?php
    require_once "../conexion.php";
    date_default_timezone_set('America/Guatemala');

    $opcion = $_POST['opcion'];
    $ai = 2023;
    $af = 2024;

    switch ($opcion) {
        case 1: #Comparativo de ventas por mes cadena
            $query = " SELECT *
                        FROM (SELECT
                                TO_CHAR(INV.CREATED_DATE,'YYYY') ANIO,
                                TO_CHAR(INV.CREATED_DATE,'MM')MES,
                                ROUND(SUM(CASE WHEN INV.INVC_TYPE = 0 THEN ((I_I.PRICE/1.12) * I_I.QTY)
                                              WHEN INV.INVC_TYPE = 2 THEN ((I_I.PRICE/1.12) * I_I.QTY)* -1 END),2) AS VENTA
                              FROM INVOICE INV
                                INNER JOIN INVC_ITEM I_I
                                  ON INV.INVC_SID = I_I.INVC_SID
                                INNER JOIN EMPLOYEE EMP
                                  ON INV.CLERK_ID = EMP.EMPL_ID
                                INNER JOIN INVN_SBS SBS
                                  ON I_I.ITEM_SID = SBS.ITEM_SID AND INV.SBS_NO = SBS.SBS_NO
                              WHERE INV.SBS_NO = 1
                              AND INV.STORE_NO NOT IN(000,100,104,109)
                              AND TO_CHAR(INV.CREATED_DATE,'YYYYMM') IN('202301','202302','202303','202401','202402','202403')
                              GROUP BY
                                TO_CHAR(INV.CREATED_DATE,'YYYY'),
                                TO_CHAR(INV.CREATED_DATE,'MM')
                              ORDER BY 1,2)
                        PIVOT
                        (
                        SUM(VENTA)
                        FOR ANIO IN ('2023','2024')
                        )
                        ORDER BY MES";
            $consulta = oci_parse(OracleRpro(),$query);
            oci_execute($consulta);
            $ventas = oci_fetch_all($consulta,$resultado);
            oci_free_statement($consulta);
            break;
        case 2: #Comparativo mes region
            $query = "SELECT *
                      FROM(SELECT
                            CASE WHEN TIENDA = 4 OR TIENDA = 12 OR TIENDA = 14 OR TIENDA = 17 OR TIENDA = 28 OR TIENDA = 32 OR TIENDA = 34 OR TIENDA = 40 OR TIENDA = 60 OR TIENDA = 63 OR TIENDA = 3 OR TIENDA = 7 OR TIENDA = 9 OR TIENDA = 25 OR TIENDA = 37 OR TIENDA = 38 OR TIENDA = 42 OR TIENDA = 52 OR TIENDA = 55 OR TIENDA = 69 OR TIENDA = 11 OR TIENDA = 15 OR TIENDA = 22 OR TIENDA = 30 OR TIENDA = 31 OR TIENDA = 41 OR TIENDA = 45 OR TIENDA = 56 OR TIENDA = 58 OR TIENDA = 64 OR TIENDA = 65 THEN 'GIOVANNI' ELSE 'CHRISTIAN' END AS GERENTE,
                            ANIO,
                            MES,
                            VENTA
                          FROM (SELECT
                            INV.STORE_NO TIENDA,
                            TO_CHAR(INV.CREATED_DATE,'YYYY') ANIO,
                            TO_CHAR(INV.CREATED_DATE,'MM') MES,
                            ROUND(SUM(CASE WHEN INV.INVC_TYPE = 0 THEN ((I_I.PRICE/1.12) * I_I.QTY)
                                          WHEN INV.INVC_TYPE = 2 THEN ((I_I.PRICE/1.12) * I_I.QTY)* -1 END),2) AS VENTA
                          FROM INVOICE INV
                            INNER JOIN INVC_ITEM I_I
                              ON INV.INVC_SID = I_I.INVC_SID
                          WHERE INV.SBS_NO = 1
                          AND INV.STORE_NO NOT IN(000,100,104)
                          AND TO_CHAR(INV.CREATED_DATE,'YYYYMM') BETWEEN '202401' AND '202402'
                          GROUP BY
                            TO_CHAR(INV.CREATED_DATE,'YYYY'),
                            TO_CHAR(INV.CREATED_DATE,'MM'),
                            INV.STORE_NO
                          ORDER BY 2,3,1))
                      PIVOT
                      (
                      SUM(VENTA)
                      FOR GERENTE IN ('GIOVANNI','HOSMAR')
                      )
                      ORDER BY MES";
            $consulta = oci_parse(OracleRpro(),$query);
            oci_execute($consulta);
            $ventas = oci_fetch_all($consulta,$resultado);
            oci_free_statement($consulta);
            break;
        case 3: #Comparativo semana distrito


            $yw = "";
            $y = date('Y') - 1;
            $w = date('W') - 2;
            for ($i = 0; $i < 2; $i++) {
              for ($j = 0; $j < 3; $j++) {
                $yw .= ($y + $i) . ($w + $j) . ",";
              }
            }
            $yw = substr($yw, 0, -1);


            $query = "SELECT *
                      FROM (SELECT
                        TO_CHAR(INV.CREATED_DATE,'IYYY') ANIO,
                        TO_CHAR(INV.CREATED_DATE,'IW') SEMANA,
                        ROUND(SUM(CASE WHEN INV.INVC_TYPE = 0 THEN ((I_I.PRICE/1.12) * I_I.QTY)
                                      WHEN INV.INVC_TYPE = 2 THEN ((I_I.PRICE/1.12) * I_I.QTY)* -1 END),2) AS VENTA
                      FROM INVOICE INV
                        INNER JOIN INVC_ITEM I_I
                          ON INV.INVC_SID = I_I.INVC_SID
                      WHERE INV.SBS_NO = 1
                      AND INV.STORE_NO IN(5,21,24,33,39,44,53,57,62,67,68)
                      AND TO_CHAR(INV.CREATED_DATE,'IYYYIW') IN(202216,202217,202218,202316,202317,202318)
                      GROUP BY
                        TO_CHAR(INV.CREATED_DATE,'IYYY'),
                        TO_CHAR(INV.CREATED_DATE,'IW')
                      ORDER BY 1,2)
                      PIVOT
                      (
                      SUM(VENTA)
                      FOR ANIO IN ('2022','2023')
                      )
                      ORDER BY SEMANA
                      ";
            $consulta = oci_parse(OracleRpro(), $query);
            oci_execute($consulta);
            $ventas = oci_fetch_all($consulta, $resultado);
            oci_free_statement($consulta);
            break;

        case 4: #Top 5 Proveedores cadena semana 50
            $y = date('Y');
            $w = date('W') + 1;
            $yw = $y.$w;
            $query = "select PROVEEDOR,  ROUND(SUM(VENTAS),2) VENTA FROM 
						(select d.sbs_no,EXTRACT(YEAR FROM d.created_Datetime)ANIO,  v.vend_code CODIGO,v.vend_name PROVEEDOR, 
						 SUBSTR(I.DESCRIPTION2,1,6) ESTILO, 
                       SUBSTR(I.DESCRIPTION2,8,2)  GRUPO,
                       SUBSTR(I.DESCRIPTION2,11,2) COLOR, 
						sum(case when d.receipt_type=0 then (di.qty)
						when d.receipt_type=1 then (di.qty)*-1 end) PARES,
                        
                           NVL(sum(case when D.receipt_type=0 then ((DI.price-( DI.price*NVL(D.disc_perc,0)/100))*(DI.qty))/1.12 
									when D.receipt_type=1 then ((DI.price-( DI.price*NVL(D.disc_perc,0)/100))*(DI.qty))/1.12*-1 end ),0) as ventaS 
                                    
						from rps.document d 
						inner join rps.document_item di on (d.sid=di.doc_sid)
						inner join rps.invn_sbs_item i on (i.sid=di.invn_sbs_item_sid and i.sbs_sid=d.subsidiary_sid)
						left join rps.vendor v on (i.vend_sid=v.sid and i.sbs_sid=v.sbs_sid)
						left join rps.invn_sbs_extend u on (i.sid=u.invn_sbs_item_sid)
						where d.status=4
						AND I.ALU <> '134810'
						AND V.VEND_CODE not IN ('106','127')
						and d.receipt_type<>2
						and d.sbs_no=1 
                        and d.store_no in(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72)
					
					and  EXTRACT(YEAR FROM D.CREATED_dATETIME)|| TO_CHAR(trunc(D.CREATED_DATETIME,'d'),'IW')+1 = '$yw'
					group by d.sbs_no, EXTRACT(YEAR FROM d.created_Datetime), v.vend_code,v.vend_name, SUBSTR(I.DESCRIPTION2,1,6) , 
                       SUBSTR(I.DESCRIPTION2,8,2)  ,
                       SUBSTR(I.DESCRIPTION2,11,2) 
					)
					GROUP BY  PROVEEDOR
					ORDER BY VENTA DESC
FETCH FIRST 5 ROWS ONLY";
            $consulta = oci_parse(Oracle(), $query);
            oci_execute($consulta);
            $ventas = oci_fetch_all($consulta, $resultado);
            oci_free_statement($consulta);
            break;
        default:
            break;
    }

    print json_encode($resultado,JSON_UNESCAPED_UNICODE);