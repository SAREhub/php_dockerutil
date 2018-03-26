#!/usr/bin/env bash

. ./bin/dockerutil

echo "#print_header(): "
dockerutil::print_header "text"

echo "#print_arrow(): "
dockerutil::print_arrow "text"

echo "#print_success(): "
dockerutil::print_success "text"

echo "#print_error(): "
dockerutil::print_error "text"

echo "#print_warning(): "
dockerutil::print_warning "text"

echo "#print_underline(): "
dockerutil::print_underline "text"

echo "#print_bold(): "
dockerutil::print_bold "text"

echo "#print_note(): "
dockerutil::print_note "text"

echo "#is_cygwin_env():"
$(dockerutil::is_cygwin_env) && echo 'true' || echo 'false'

echo "#pwd():"
dockerutil::pwd

echo "#get_docker_shared_project_dir():"
dockerutil::get_docker_shared_project_dir




