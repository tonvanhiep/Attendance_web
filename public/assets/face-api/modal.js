// Learn Template literals: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
// Learn about Modal: https://getbootstrap.com/docs/5.0/components/modal/

var modalWrap = null;
/**
 *
 * @param {string} title
 * @param {string} description content of modal body
 * @param {string} yesBtnLabel label of Yes button
 * @param {string} noBtnLabel label of No button
 * @param {function} callback_Yes callback function when click Yes button
 * @param {function} callback_No callback function when click No button
 */
export const showModal = (
    title,
    description,
    yesBtnLabel,
    noBtnLabel,
    callback_Yes,
    callback_No
    // callback_Close,
) => {
    if (modalWrap !== null) {
        modalWrap.remove();
        $(".modal-backdrop").remove();
    }

    modalWrap = document.createElement("div");
    modalWrap.innerHTML = `
    <div class="modal fade" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title">${title}</h5>
          </div>
          <div class="modal-body">
            <p>${description}</p>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" id="yesBtn" class="btn btn-primary modal-success-btn" data-bs-dismiss="modal">${yesBtnLabel}</button>
            <button type="button" id="noBtn" class="btn btn-secondary" data-bs-dismiss="modal">${noBtnLabel}</button>
          </div>
        </div>
      </div>
    </div>
  `;

    modalWrap.querySelectorAll("button").forEach((occurence) => {
        let id = occurence.getAttribute("id");
        occurence.onclick = () => {
            switch (id) {
                case "yesBtn": {
                    console.log("click yes");
                    callback_Yes();
                    break;
                }
                case "noBtn": {
                    console.log("click no");
                    callback_No();
                    break;
                }
                // case "closeBtn": {
                //     console.log("click close");
                //     callback_Close();
                //     break;
                // }
            }
        };
    });

    document.body.append(modalWrap);

    var modal = new bootstrap.Modal(modalWrap.querySelector(".modal"));
    modal.show();
};
