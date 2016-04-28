<style type="text/css">
    #board {
        text-align: center;
        border-spacing: 0;
        font-family: "Arial Unicode MS";
        border-collapse: collapse;
        border-color: #888888;
        border-width: 1pt;
    }
    #board tr {
        vertical-align: bottom;
    }
    #board td.num {
        vertical-align: middle;
        width: 12pt;<h1>Ajedrez</h1>
        
        font-size: 9pt;
    }
    #board td.square {
        width: 38pt;
        height: 38pt;
        font-size: 28pt;
        padding: 0;
        border-collapse: collapse;
        border-style: solid;
        border-width: 1pt 0 0 0;
        border-color: #888888;
        vertical-align: middle;
    }
    #board td.square.light {
        background-color: #FFFFFF;
    }
    #board td.square.dark {
        background-color: #DDDDDD;
    }
</style>

<center>

<p>Juegan las {$turnStr}. Encuentre la mejor jugada.</p>

{$board}

<p>Dificultad: {$level}</p>

<p>Seleccione aqu&iacute; para ver la soluci&oacute;n: [<font color="white">{$solution}</font>]</p>

{space10}

<p>Otras dificultades:</p>
{if $level ne "Fácil"}{button href="AJEDREZ FACIL" caption="F&aacute;cil"}{/if}
{if $level ne "Intermedio"}{button href="AJEDREZ" caption="Mediano"}{/if}
{if $level ne "Difícil"}{button href="AJEDREZ DIFICIL" caption="D&iacute;ficil"}{/if}

</center>
