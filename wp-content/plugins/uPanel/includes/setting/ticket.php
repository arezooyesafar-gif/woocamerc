<div class="mdl-card mdl-shadow--2dp tab" id="ticket-tab" style="width: 100%;display:none">
                            <div class="mdl-card__supporting-text">
                            <p class="hd-upanel-label">دپارتمان ها</p>
                            <div class="ticket-departmets">
                            <?php
                            $settings->departments = explode(",", urldecode($settings->departments));


                            foreach ($settings->departments as $key => $value) {
                                ?>
                                                <div>
                                                   <?php if ($key != 0) { ?> <button type="button" class="minus">-</button><?php } ?>
                                                    <input style="direction:ltr" class="mt-1" type="text"
                                                        name="departments[]" value="<?php echo $value; ?>">
                                                    <button type="button" class="plus">+</button>

                                                </div>

                                                <?php
                            }
                            ?>

                                            <div>
                                                <button type="button" class="minus">-</button>
                                                <input style="direction:ltr" class="mt-1" type="text"
                                                    name="departments[]" value="">
                                                <button type="button" class="plus">+</button>

                                            </div>

                            </div>
          


                                <p class="divider"></p>

                                <p class="hd-upanel-label">بسته شدن تیکت بعد از </p>
                                <input name="ticket_expire" type="text" value="<?php echo $settings->ticket_expire; ?>"
                                    class="hd-upanel-input"> روز
                             
                            </div>
                        </div>