    <div id="footer" style="display:none;">
        <div id="copyright">
            <a href="http://www.kaydoo.co.uk/projects/backendpro">BackendPro</a> &copy; Copyright 2008 - <a href="http://www.kaydoo.co.uk">Adam Price</a> -  All rights Reserved
        </div>
        <div id="version">
            <a href="#top"><?php print $this->lang->line('general_top')?></a> |
            <a href="<?php print base_url()?>user_guide"><?php print $this->lang->line('general_documentation')?></a> |
            Version <?php print BEP_VERSION?></div>
    </div>
</div>
<?php print $this->bep_assets->get_footer_assets();?>
<script src="<?php print base_url()?>assets/js/jplot/jquery.jqplot.js"></script>
<script src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.pieRenderer.js"></script>
<script src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.barRenderer.js"></script>
<script type="text/javascript" src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.logAxisRenderer.js"></script>
<script type="text/javascript" src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.canvasTextRenderer.js"></script>
<script type="text/javascript" src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.canvasAxisLabelRenderer.js"></script>
<script type="text/javascript" src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.canvasAxisTickRenderer.js"></script>
<script type="text/javascript" src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.dateAxisRenderer.js"></script>
<script type="text/javascript" src="<?php print base_url()?>assets/js/jplot/plugins/jqplot.categoryAxisRenderer.js"></script>
</body>
</html>