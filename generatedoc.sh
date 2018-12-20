#!/usr/bin/env bash
rm -rf docs
./vendor/apigen/apigen/bin/apigen generate -s src --destination docs
