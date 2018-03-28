#!/usr/bin/env bash

. ./bin/dockerutil


dockerutil::print_header "print_arrow()"
dockerutil::print_arrow "text"

dockerutil::print_header "print_success()"
dockerutil::print_success "text"

dockerutil::print_header  "print_error()"
dockerutil::print_error "text"

dockerutil::print_header "print_warning()"
dockerutil::print_warning "text"

dockerutil::print_header "print_underline()"
dockerutil::print_underline "text"

dockerutil::print_header "print_bold()"
dockerutil::print_bold "text"

dockerutil::print_header "print_note()"
dockerutil::print_note "text"

dockerutil::print_header "is_cygwin_env()"
$(dockerutil::is_cygwin_env) && echo 'true' || echo 'false'

dockerutil::print_header "pwd()"
dockerutil::pwd

dockerutil::print_header "get_docker_shared_project_dir()"
dockerutil::get_docker_shared_project_dir

dockerutil::print_header "dockerutil::clean_all_with_label()"
test_label="dockerutil_testenv"
docker service create --name "dockerutil_test_service" --label $test_label nginx:alpine
dockerutil::clean_all_with_label $test_label 2


