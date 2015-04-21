<!-- checkbox -->
                  <div class="form-group">
                    <label>
                      <input type="checkbox" class="flat-red" checked/>
                    </label>
                    <label>
                      <input type="checkbox" class="flat-red"/>
                    </label>
                    <label>
                      <input type="checkbox" class="flat-red" disabled/>
                      Flat green skin checkbox
                    </label>
                  </div>
<!-- radio -->
                  <div class="form-group">
                    <label>
                      <input type="radio" name="r3" class="flat-red" checked/>
                    </label>
                    <label>
                      <input type="radio" name="r3" class="flat-red"/>
                    </label>
                    <label>
                      <input type="radio" name="r3" class="flat-red" disabled/>
                      Flat green skin radio
                    </label>
                  </div>
				  
<!--script-->
<script type="text/javascript">
//Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        });
</script>