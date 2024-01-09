<?php

namespace redstar\Views;

class UsersPageView implements IView
{

    public function printOutput(array $tplData)
    {
        $header = new HeaderView();

        $header->printOutput($tplData);

        if (isset($tplData["error"])) {
            $error = $tplData["error"];
            echo "<div class='alert alert-danger mx-5 mt-3'><b>Chyba: </b>$error</div>";
        } else {
            echo "
                <h1 class='display-1 text-center fw-bold my-5'>Správa uživatelů</h1>
                <div class='overflow-x-auto my-5 mx-5'>
                    <table class='table table-striped table-dark table-bordered'>
                        <thead class='thead-light'>
                            <tr>
                                <th class='col-1 bg-primary' scope='col'>ID uživatele</th>
                                <th class='col-1 bg-primary' scope='col'>Stav uživatele</th>
                                <th class='bg-primary' scope='col'>Uživatelské jméno</th>
                                <th class='bg-primary' scope='col'>Celé jméno</th>
                                <th class='bg-primary' scope='col'>Role</th>
                                <th class='col-1 bg-primary' scope='col'>Akce</th>
                            </tr>
                            <tbody>";

            for ($i = 0; $i < sizeof($tplData["users"]); $i++) {
                $user = $tplData["users"][$i];
                $userId = $user->getId();
                $userIsBanned = $user->isBanned();
                $username = htmlspecialchars($user->getUsername());
                $userFullName = htmlspecialchars($user->getFirstname()) . " " . htmlspecialchars($user->getLastName());
                $userRoleName = $user->getRole()->getName();
                $userPermissions = $user->getRole()->getPermissions();
                $loggedUserPermissions = $tplData["user"]->getRole()->getPermissions();

                echo "<tr>";
                echo "<td>$userId</td>";

                if ($userIsBanned) {
                    echo "
                        <td class='text-danger text-center'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' fill='currentColor' class='bi bi-x-circle' viewBox='0 0 16 16'>
                              <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16'/>
                              <path d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708'/>
                            </svg>
                        </td>";
                } else {
                    echo "
                        <td class='text-success text-center'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' fill='currentColor' class='bi bi-check-circle' viewBox='0 0 16 16'>
                                <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16'></path>
                                <path d='m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05'></path>
                            </svg>
                        </td>";
                }

                echo "<td>$username</td>";
                echo "<td>$userFullName</td>";
                if ($loggedUserPermissions > $userPermissions and $loggedUserPermissions > 5) {
                    ?>
                        <td>
                        <?php echo "<select id='changeRoleSelect$i' onchange='changeUserRole($userId, $i)'>"; ?>
                            <?php
                            foreach ($tplData["roles"] as $role) {
                                $roleId = $role->getId();
                                $roleName = $role->getName();
                                $rolePermissions = $role->getPermissions();
                                if ($loggedUserPermissions > $rolePermissions) {
                                    if ($role->getName() == $userRoleName) {
                                        echo "<option value='$roleId' selected>$roleName</option>";
                                    } else {
                                        echo "<option value='$roleId'>$roleName</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                        </td>

                    <?php
                } else {
                    echo "<td>$userRoleName</td>";
                }

                echo "<td><div class='d-inline-flex gap-2'>";
                if ($loggedUserPermissions > $userPermissions) {
                    if (!$userIsBanned) {
                        echo "<button class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#banModal' onclick='targetUser($userId)'>Zabanovat</button>";
                    } else {
                        echo "<button class='btn btn-success' data-bs-toggle='modal' data-bs-target='#unbanModal' onclick='targetUser($userId)'>Odbanovat</button>";
                    }

                    if ($loggedUserPermissions > 5) {
                        echo "<button class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#removeUserModal' onclick='targetUser($userId)'>Odstranit</button>";
                    }
                }
                echo  "</div></td>";
                echo "</tr>";
            }

            echo "                   
                            </tbody>
                        </thead>
                    </table>
                </div>
            ";

            ?>
            <div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Potvrzení akce</h5>
                        </div>
                        <div class="modal-body">
                            Opravdu si přejete tohoto hráče zabanovat? Hráč se nebude moci přes svůj účet připojit.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" onclick="confirmBanUser()">Zabanovat</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="unbanModal" tabindex="-1" role="dialog" aria-labelledby="unbanModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Potvrzení akce</h5>
                        </div>
                        <div class="modal-body">
                            Opravdu si přejete tohoto hráče odbanovat?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="confirmUnbanUser()">Odbanovat</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="removeUserModal" tabindex="-1" role="dialog" aria-labelledby="removeUserModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Potvrzení akce</h5>
                        </div>
                        <div class="modal-body">
                            Opravdu si přejete odstranit veškeré údaje uživatele včetně všech odehraných a vedených zápasů?
                            <p class="text-danger">Tato akce je nevratná!!</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" onclick="confirmCompletelyRemoveUser()">Permanentně odstranit</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
            <script src="../../../web-semestralka/app/Resources/scripts/users-page.js" type="application/javascript"></script>
<?php
        }

        $header->getHTMLFooter();
    }
}