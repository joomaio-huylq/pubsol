<tr>
    <td>
        <input class="checkbox-item" type="checkbox" name="ids[]" value="<?php echo $this->item['id']; ?>">
    </td>
    <td>
        <a href="#"
            class="show_data" 
            data-id="<?php echo  $this->item['id'] ?>" 
            data-title="<?php echo  $this->item['title']  ?>" 
            data-url="<?php echo   $this->item['url'] ?>" 
            data-bs-placement="top" 
            data-bs-toggle="modal" 
            data-bs-target="#Popup_form_task">
            <?php echo  $this->item['title']  ?>
        </a>
    </td>
    <td><a href="<?php echo $this->item['url']; ?>"><?php echo   $this->item['url'] ?></a></td>
    <td><?php echo   $this->item['created_at'] ?></td>
    <td>
        <a href="#>" 
            class="fs-4 me-1 show_data"
            data-id="<?php echo  $this->item['id'] ?>" 
            data-title="<?php echo  $this->item['title']  ?>" 
            data-url="<?php echo   $this->item['url']?>"
            data-bs-placement="top" 
            data-bs-toggle="modal" 
            data-bs-target="#Popup_form_task">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
        <a data-id="<?php echo  $this->item['id'] ?>" style="color:#3b7ddd;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" class="delete fs-4 ps-1 border-0 bg-transparent button_delete_item"><i class="fa-solid fa-trash"></i></a>
    </td>
</tr>