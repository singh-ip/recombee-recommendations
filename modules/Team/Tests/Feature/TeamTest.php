<?php

use Modules\Team\Models\Team;

beforeEach(function () {
    $this->admin = createSuperAdmin();
    $this->user = createUser();
});

test('superadmin can list teams', function () {
    $this->actingAs($this->admin)->get(route('module.team.list_teams'))->assertOk();
})->group('modules', 'team');

test('superadmin can create teams', function () {
    $name = fake()->domainName;
    $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);
})->group('modules', 'team');

test('superadmin cannot create team with duplicate name', function () {
    $name = fake()->domainName;
    $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);
    $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertInvalid();
})->group('modules', 'team');

test('superadmin can view team', function () {
    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);

    $id = json_decode($response->content(), true)['data']['id'];

    $this->actingAs($this->admin)->get(route('module.team.show_team', ['id' => $id]))->assertOk();
})->group('modules', 'team');


test('superadmin can edit team name', function () {
    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);

    $id = json_decode($response->content(), true)['data']['id'];

    $name = fake()->domainName;
    $this->actingAs($this->admin)->put(route('module.team.edit_team', ['id' => $id]), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);
})->group('modules', 'team');

test('superadmin cannot edit team using duplicate name', function () {
    $name1 = fake()->domainName;
    $name2 = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name1,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name1]);

    $id = json_decode($response->content(), true)['data']['id'];

    $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name2,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name2]);

    $this->actingAs($this->admin)->put(route('module.team.edit_team', ['id' => $id]), [
        'name' => $name2,
    ])->assertInvalid();
})->group('modules', 'team');

test('superadmin can delete empty team ', function () {
    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);

    $id = json_decode($response->content(), true)['data']['id'];

    $this->actingAs($this->admin)->delete(route('module.team.delete_team', ['id' => $id]))->assertOk();
    $this->assertSoftDeleted('teams', ['name' => $name]);
})->group('modules', 'team');

test('superadmin cannot delete non-empty team', function () {
    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);

    $id = json_decode($response->content(), true)['data']['id'];

    $team = Team::findOrFail($id);
    $team->users()->attach($this->user);
    $team->save();

    $this->actingAs($this->admin)->delete(route('module.team.delete_team', ['id' => $id]))->assertBadRequest();
    $this->assertDatabaseHas('teams', ['name' => $name]);
})->group('modules', 'team');

test('superadmin cannot delete team - with invalid team id', function () {
    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);

    $id = json_decode($response->content(), true)['data']['id'];

    $team = Team::findOrFail($id);
    $team->users()->attach($this->user);
    $team->deleted_at = now();
    $team->save();

    $this->actingAs($this->admin)->delete(route('module.team.delete_team', ['id' => $id]))->assertUnprocessable();

})->group('modules', 'team');

test('superadmin can assign and de-assing user-team relation', function () {
    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);
    $id = json_decode($response->content(), true)['data']['id'];
    $this->actingAs($this->admin)->post(
        route('module.team.change_team_assignment', ['team' => $id, 'user' => $this->user->id])
    )->assertOk();
    $this->actingAs($this->admin)->post(
        route('module.team.change_team_assignment', ['team' => $id, 'user' => $this->user->id, 'detach' => 1])
    )->assertOk();
})->group('modules', 'team');

test('User cannot leave last team', function () {
    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);

    $id = json_decode($response->content(), true)['data']['id'];

    $team = Team::findOrFail($id);
    $team->users()->attach($this->user);
    $team->save();

    $this->actingAs($this->user)->post(route('module.team.leave_team', ['id' => $id]))->assertBadRequest();

    $name = fake()->domainName;
    $response = $this->actingAs($this->admin)->post(route('module.team.create_team'), [
        'name' => $name,
    ])->assertOk();
    $this->assertDatabaseHas('teams', ['name' => $name]);

    $id = json_decode($response->content(), true)['data']['id'];

    $team = Team::findOrFail($id);
    $team->users()->attach($this->user);
    $team->save();

    $this->actingAs($this->user)->post(route('module.team.leave_team', ['id' => $id]))->assertOk();
})->group('modules', 'team');

test('cannot delete default team ', function () {
    $team = Team::firstWhere('default', true);
    $this->assertNotNull($team);
    $this->actingAs($this->admin)->delete(route('module.team.delete_team', ['id' => $team->id]))->assertBadRequest();
})->group('modules', 'team');
