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
        width: 12pt;
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

<h1>Ajedrez</h1>

<p>Juegan las {$turnStr}. Encuentre la mejor continuaci&oacute;n.</p>

{$board}

<p>Nivel: {$level}</p>

<p>Seleccione este texto para descubrir la soluci&oacute;n: [<span style="color: white">{$solution}</span>]</p>

</center>