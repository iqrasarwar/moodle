// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * TODO describe module delete_ajax
 *
 * @module     local_greetings/delete_ajax
 * @copyright  2023 true
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from "core/modal_factory";
import ModalEvents from "core/modal_events";
import { call as fetchMany } from "core/ajax";

const deleteMessage = (id) =>
  fetchMany([
    {
      methodname: "local_greetings_delete_message",
      args: { id },
    },
  ])[0];

export const init = async ({ id }) => {
  const modal = await ModalFactory.create({
    type: ModalFactory.types.SAVE_CANCEL,
    title: "Dlete Message",
    body: "You are going to delete message with id " + id + " ?",
  });
  modal.getRoot().on(ModalEvents.save, async () => {
    window.console.log(1323);
    const response = await deleteMessage(id);
    window.console.log(response);
    window.location.href =
      "http://localhost:8888/moodle/moodle/local/greetings/index.php";
  });
  modal.show();
};
